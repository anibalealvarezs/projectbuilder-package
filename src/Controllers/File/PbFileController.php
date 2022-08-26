<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\File;

use Anibalealvarezs\Projectbuilder\Controllers\PbBuilderController;
use Anibalealvarezs\Projectbuilder\Utilities\PbUtilities;
use Anibalealvarezs\Projectbuilder\Utilities\PbCache;
use App\Http\Requests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

use Auth;

class PbFileController extends PbBuilderController
{
    function __construct(Request $request, $crud_perms = false)
    {
        $this->varsObject([
            'keys' => [
                'level' => 'File'
            ],
            'validationRules' => [
                'name' => ['max:128'],
                'description' => [],
                'alt' => [],
            ],
            'modelExclude' => ['file'],
            'showId' => true,
            'defaults' => [
                'status' => 1,
            ],
        ]);
        // Parent construct
        parent::__construct($request, true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse|JsonResponse|null
     */
    public function store(Request $request): Redirector|RedirectResponse|Application|JsonResponse|null
    {
        $this->pushValidationRules([
            'file' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,svg,pdf', 'max:8192'],
            'module' => ['nullable', 'max:32'],
            'permission' => ['required', 'max:32'],
        ]);

        // Validation
        if ($failed = $this->validateRequest($this->vars->validationRules, $request)) {
            return $failed;
        }

        $file = $request->file('file');

        // Process
        try {
            // Build model
            $model = resolve($this->vars->level->modelPath)->setLocale(app()->getLocale());
            // Add requests
            $model = $this->processModelRequests(
                validationRules: $this->vars->validationRules,
                request: $request,
                replacers: $this->vars->replacers,
                model: $model
            );

            // Default module for file storage
            $model->module = $model->module ?: $this->vars->level->name;

            $name = PbUtilities::checkName(
                dir: $model->module ?: $this->vars->level->name,
                name: ($model->name ?: $file->getClientOriginalName()),
                extension: $file->extension()
            );
            if (!$name) {
                return $this->redirectResponseCRUDFail($request, 'create', 'No valid name for file');
            }
            if ($newfile = PbUtilities::saveFile($file, $model->module, $name)) {
                $model->name = $name;
                $model->mime_type = $newfile->getMimeType();
                $model->user_id = Auth::user()->id;
                $model->hash = md5($newfile->getContent());
                if (!$model->save()) {
                    return $this->redirectResponseCRUDFail($request, 'create', "Error saving {$this->vars->level->name}");
                }

                PbCache::clearIndex(
                    package: $this->vars->helper->package,
                    models: $this->vars->level->names,
                );
            } else {
                return $this->redirectResponseCRUDFail($request, 'create', 'File could not be saved');
            }

            return $this->redirectResponseCRUDSuccess($request, 'create');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'create', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Application|Redirector|RedirectResponse|JsonResponse|null
     */
    public function update(Request $request, int $id): Redirector|RedirectResponse|Application|JsonResponse|null
    {
        // Validation
        if ($failed = $this->validateRequest($this->vars->validationRules, $request)) {
            return $failed;
        }

        // Set cache/methods arguments
        $this->initArgs([
            'function' => 'update',
        ]);

        // Process
        try {
            // Build model
            if (!$model = $this->vars->level->modelPath::find($id)) {
                return $this->redirectResponseCRUDFail($request, 'update', "Error finding {$this->vars->level->name} with id $id");
            }
            // Check Permissions
            $this->checkPermissions($model);
            // Set Locale
            $model->setLocale(app()->getLocale());
            // Prepare file name and extension
            $currentName = $model->name;
            $extension = PbUtilities::getFileExtension($model->name);
            // Build requests
            $model = $this->processModelRequests(
                validationRules: $this->vars->validationRules,
                request: $request,
                replacers: $this->vars->replacers,
                model: $model
            );
            $updateAuthor = true;
            if (!$model->name) {
                $model->name = $currentName;
            } elseif ($currentName != $model->name) {
                $model->name = PbUtilities::checkName($model->module, $model->name, $extension, "", $currentName);
                if ($currentName != $model->name) {
                    $updateAuthor = PbUtilities::updateFile($model->module, $currentName, $model->name);
                }
            }
            if ($updateAuthor) {
                $model->user_id = Auth::user()->id;
                // Model update
                if (!$model->save()) {
                    return $this->redirectResponseCRUDFail($request, 'update', "Error updating {$this->vars->level->name}");
                }

                PbCache::clearModel(
                    package: $this->vars->helper->package,
                    models: $this->vars->level->names,
                    modelId: $id,
                );
            } else {
                return $this->redirectResponseCRUDFail($request, 'update', 'File could not be saved');
            }

            return $this->redirectResponseCRUDSuccess($request, 'update');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'update', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return Application|Redirector|RedirectResponse|JsonResponse
     */
    public function destroy(Request $request, int $id): Redirector|RedirectResponse|Application|JsonResponse
    {
        // Set cache/methods arguments
        $this->initArgs([
            'function' => 'destroy',
        ]);
        // Process
        try {
            if (!$model = $this->vars->level->modelPath::find($id)) {
                return $this->redirectResponseCRUDFail($request, 'delete', "Error finding {$this->vars->level->name}");
            }
            // Check Permissions
            $this->checkPermissions($model);
            if (PbUtilities::deleteFile($model->module, $model->name)) {
                // Model delete
                if (!$model->delete()) {
                    return $this->redirectResponseCRUDFail($request, 'delete', "Error deleting {$this->vars->level->name}");
                }

                PbCache::clearAll();
            }

            return $this->redirectResponseCRUDSuccess($request, 'delete');
        } catch (Exception $e) {
            return $this->redirectResponseCRUDFail($request, 'delete', $e->getMessage());
        }
    }
}
