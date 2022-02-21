<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Anibalealvarezs\Projectbuilder\Facades\PbDebugbarFacade as Debug;
use Anibalealvarezs\Projectbuilder\Models\PbCountry;
use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use Anibalealvarezs\Projectbuilder\Models\PbModule;
use Anibalealvarezs\Projectbuilder\Utilities\PbCache;
use Anibalealvarezs\Projectbuilder\Utilities\PbShares;
use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;

use Anibalealvarezs\Projectbuilder\Utilities\PbUtilities;
use Auth;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use ReflectionException;
use Inertia\Response as InertiaResponse;

trait PbControllerTrait
{
    /**
     * Remove the specified resource from storage.
     *
     * @return array
     */
    protected function globalInertiaShare(): array
    {
        return PbShares::list([
            'api_data',
            'navigations',
            'languages',
            'locale',
            'cache',
            'debug_status',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param array|string $permissions
     * @return array
     */
    protected function getAllowed(array|string $permissions): array
    {
        $allowed = [];
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                $allowed[$permission] = app(PbCurrentUser::class)->hasPermissionTo($permission);
            }
        } else {
            $allowed[$permissions] = app(PbCurrentUser::class)->hasPermissionTo($permissions);
        }

        return $allowed;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param array $validationRules
     * @param Request $request
     * @return Application|Redirector|RedirectResponse|null
     */
    protected function validateRequest(array $validationRules, Request $request): Redirector|RedirectResponse|Application|null
    {
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $current = "";
            foreach ($errors->all() as $message) {
                $current = $message;
            }

            return $this->redirectResponse($request, $current);
        }

        return null;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param string $flashMessage
     * @param string $route
     * @param string $destiny
     * @param string $flashStyle
     * @param bool $withInput
     * @return RedirectResponse|Application|Redirector
     */
    protected function redirectResponse(
        Request $request,
        string $flashMessage,
        string $route = 'back',
        string $destiny = "",
        string $flashStyle = 'danger',
        bool $withInput = true
    ): RedirectResponse|Application|Redirector {
        $request->session()->flash('flash.banner', $flashMessage);
        $request->session()->flash('flash.bannerStyle', $flashStyle);

        $redirect = redirect();

        switch ($route) {
            case 'route':
                if (is_array($destiny)) {
                    $redirect = $redirect->route($destiny['route'], $destiny['id']);
                } else {
                    $redirect = $redirect->route($destiny);
                }
                break;
            case 'back':
                $redirect = $redirect->back();
                break;
            default:
                break;
        }
        if ($withInput) {
            $redirect = $redirect->withInput();
        }
        return $redirect;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param string $process
     * @return RedirectResponse|Application|Redirector
     */
    protected function redirectResponseCRUDSuccess(
        Request $request,
        string $process
    ): Redirector|Application|RedirectResponse {
        return $this->redirectResponse(
            $request,
            $this->buildCRUDResponseMessage($process, 'success'),
            'route',
            $this->vars->level->names . '.index',
            'success',
            false
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param string $process
     * @param string $error
     * @return RedirectResponse|Application|Redirector
     */
    protected function redirectResponseCRUDFail(
        Request $request,
        string $process,
        string $error
    ): Redirector|Application|RedirectResponse {
        return $this->redirectResponse(
            $request,
            $this->buildCRUDResponseMessage($process, 'fail', $error)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $process
     * @param string $result
     * @param string $error
     * @return string
     */
    public function buildCRUDResponseMessage(string $process, string $result, string $error = ""): string
    {
        return $this->vars->level->key . match ($process) {
                'create' => match ($result) {
                    'success' => ' created successfully!',
                    'fail' => ' could not be created! ' . $error,
                },
                'update' => match ($result) {
                    'success' => ' updated successfully!',
                    'fail' => ' could not be updated! ' . $error,
                },
                'delete' => match ($result) {
                    'success' => ' deleted successfully!',
                    'fail' => ' could not be deleted! ' . $error,
                },
                'sort' => match ($result) {
                    'success' => ' sorted successfully!',
                    'fail' => ' could not be sorted! ' . $error,
                },
                'enable' => match ($result) {
                    'success' => ' enabled successfully!',
                    'fail' => ' could not be enabled! ' . $error,
                },
                'disable' => match ($result) {
                    'success' => ' disabled successfully!',
                    'fail' => ' could not be disabled! ' . $error,
                },
                default => ' model task has no valid response. Error: ' . $error,
            };
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $type
     * @param array $keys
     * @return string
     */
    protected function buildFile(string $type, array $keys): string
    {
        return match ($type) {
            'show' => 'Show' . $keys['singular'],
            'create' => 'Create' . $keys['singular'],
            'edit' => 'Edit' . $keys['singular'],
            default => $keys['plural'],
        };
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $result
     * @param $msg
     * @return JsonResponse
     */
    public function handleJSONResponse($result, $msg): JsonResponse
    {
        $res = [
            'success' => true,
            'data' => $result,
            'message' => $msg,
        ];
        return response()->json($res);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $error
     * @param array $errorMsg
     * @param int $code
     * @return JsonResponse
     */
    public function handleJSONError($error, array $errorMsg = [], int $code = 404): JsonResponse
    {
        $res = [
            'success' => false,
            'message' => $error,
        ];
        if (!empty($errorMsg)) {
            $res['data'] = $errorMsg;
        }
        return response()->json($res, $code);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Builder $query
     * @param array $defaultOrder
     * @return LengthAwarePaginator
     */
    public function paginateAndOrder(
        Builder $query,
        array $defaultOrder = []
    ): LengthAwarePaginator
    {
        $config = (isset($this->vars->config) && $this->vars->config) ? $this->vars->config : $this->vars->level->modelPath::getCrudConfig(true);
        if (!$this->vars->runArgs['pagination']['perpage'] && isset($config['pagination']['per_page']) && $config['pagination']['per_page']) {
            $this->vars->pagination['perpage'] = $config['pagination']['per_page'];
        }
        if ($this->vars->runArgs['pagination']['orderby']) {
            $query->orderBy($this->vars->runArgs['pagination']['field'], $this->vars->runArgs['pagination']['order']);
        } else {
            foreach ($defaultOrder as $key => $value) {
                $query->orderBy($key, $value);
            }
        }
        return $query->paginate($this->vars->runArgs['pagination']['perpage'] ?: (getConfigValue('_DEFAULT_TABLE_SIZE_') ?: 10), ['*'], 'page', $this->vars->runArgs['pagination']['page'] ?: 1);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Builder|null $query
     * @param array $defaultOrder
     * @return LengthAwarePaginator|Collection
     */
    public function buildPaginatedAndOrderedModel(
        Builder $query = null,
        array $defaultOrder = []
    ): LengthAwarePaginator|Collection
    {
        if (!isset($this->vars->level->modelPath::$sortable) || !$this->vars->level->modelPath::$sortable) {
            return $this->paginateAndOrder(
                query: $query ?: $this->vars->level->modelPath::withPublicRelations(),
                defaultOrder: $defaultOrder
            );
        } else {
            return $query->get();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $customArgs
     * @return array
     */
    public function run($customArgs): array
    {
        return PbCache::run(...sortArrayByKeys([...$this->vars->runArgs, ...$customArgs], PbCache::argsOrder()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $return
     * @param $name
     * @param array $args
     * @param Closure|null $pre
     * @param Closure|null $post
     * @return void
     */
    public function measuredRun(&$return, $name, array $args = [], Closure $pre = null, Closure $post = null)
    {
        Debug::measure(
            label: $name,
            closure: function() use (&$return, $args, $pre, $post) {
                if (is_closure($pre)) { $pre(); }
                $cached = $this->run($args);
                if (is_closure($post)) { $post(); }
                $return = $cached['data'];
                $this->vars->cacheObjects[$cached['tags']][] = $cached['keys'];
            }
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     * @throws ReflectionException
     */
    public function setAdditionalVars()
    {
        match($this->vars->runArgs['function']) {
            'index' => call_user_func(function() {
                $this->vars->allowed = [
                    'create ' . $this->vars->level->names => 'create',
                    'update ' . $this->vars->level->names => 'update',
                    'delete ' . $this->vars->level->names => 'delete',
                ];
                if (!isset($this->vars->config['fields']['actions'])) {
                    $this->vars->config['fields']['actions'] = [
                        'update' => [],
                        'delete' => []
                    ];
                }
                if (!isset($this->vars->config['model'])) {
                    $this->vars->config['model'] = $this->vars->level->modelPath;
                }
                if (!isset($this->vars->config['pagination']['page']) || !$this->vars->config['pagination']['page']) {
                    $this->vars->config['pagination']['page'] = $this->vars->runArgs['pagination']['page'];
                }
                $this->vars->sortable = $this->vars->sortable && app(PbCurrentUser::class)->hasPermissionTo('update '.resolve($this->vars->level->modelPath)->getTable());
                $this->vars->formconfig = ($this->vars->config['form'] ?? []);
                $this->vars->pagination = !$this->vars->sortable ? $this->vars->config['pagination'] : [];
                $this->vars->heading = $this->vars->config['heading'];
                $this->vars->orderby = !$this->vars->sortable && $this->vars->runArgs['pagination']['orderby'] ?
                    ['field' => $this->vars->runArgs['pagination']['field'], 'order' => $this->vars->runArgs['pagination']['order']] :
                    [];

                // Set enabled actions
                $this->measuredRun(return: $this->vars->config['enabled_actions'], name: getClassName(__CLASS__) . ' - model config additional', args: [
                    'closure' => fn() => PbShares::allowed($this->vars->allowed)['allowed'],
                    'modelFunction' => 'sharesAllowed', // getFunctionName(__CLASS__, __FUNCTION__),
                ]);
            }),
        };
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $args
     * @return void
     */
    public function initArgs($args)
    {
        $this->vars->runArgs['class'] = ($args['class'] ?? 'builder_controller');
        if (!isset($this->vars->runArgs['type'])) { $this->vars->runArgs['type'] = ($args['type'] ?? 'controller'); }
        if (!isset($this->vars->runArgs['function'])) { $this->vars->runArgs['function'] = ($args['function'] ?? 'index'); }
        if (!isset($this->vars->runArgs['pagination'])) {
            $this->vars->runArgs['pagination'] = ($args['pagination'] ?? ['page' => 0, 'perpage' => 0, 'orderby' => null, 'field' => 'id', 'order' => 'asc']);
        }
        if (!isset($this->vars->runArgs['modelId'])) { $this->vars->runArgs['modelId'] = ($args['modelId'] ?? 0); }
        if (!isset($this->vars->runArgs['byRoles'])) { $this->vars->runArgs['byRoles'] = ($args['byRoles'] ?? false); }
        if (!isset($this->vars->runArgs['byUser'])) { $this->vars->runArgs['byUser'] = ($args['byUser'] ?? false); }
        if (!isset($this->vars->runArgs['toArray'])) { $this->vars->runArgs['toArray'] = ($args['toArray'] ?? false); }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     * @throws ReflectionException
     */
    public function startController($name = null)
    {
        $name = $name ?? getClassName(__CLASS__);
        Debug::start($name, $name);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     * @throws ReflectionException
     */
    public function stopController($name = null)
    {
        $name = $name ?? getClassName(__CLASS__);
        Debug::stop($name);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    public function checkPermissions($model)
    {
        Debug::start('permissions_check', 'permissions check');
        match($this->vars->runArgs['function']) {
            'show' => call_user_func(function() use ($model) {
                if ($this->isUnreadableModel($model)) {
                    return $this->redirectResponseCRUDFail(request(), 'show', "This {$this->vars->level->name} cannot be shown");
                }
                if (!$model->isViewableBy(Auth::user()->id)) {
                    return $this->redirectResponseCRUDFail(request(), 'show', "You don't have permission to view this {$this->vars->level->name}");
                }
            }),
            'edit' => call_user_func(function() use ($model) {
                if ($this->isUnreadableModel($model)) {
                    return $this->redirectResponseCRUDFail(request(), 'edit', "This {$this->vars->level->name} cannot be modified");
                }
                if (!$model->isEditableBy(Auth::user()->id)) {
                    return $this->redirectResponseCRUDFail(request(), 'edit', "You don't have permission to edit this {$this->vars->level->name}");
                }
            }),
            'update' => call_user_func(function() use ($model) {
                if ($this->isUnmodifiableModel($model)) {
                    return $this->redirectResponseCRUDFail(request(), 'update', "This {$this->vars->level->name} cannot be modified");
                }
                if (!$model->isEditableBy(Auth::user()->id)) {
                    return $this->redirectResponseCRUDFail(request(), 'update', "You don't have permission to edit this {$this->vars->level->name}");
                }
            }),
            'destroy' => call_user_func(function() use ($model) {
                if ($this->isUndeletableModel($model)) {
                    return $this->redirectResponseCRUDFail(request(), 'delete', "This {$this->vars->level->name} cannot be deleted");
                }
                if (!$model->isDeletableBy(Auth::user()->id)) {
                    return $this->redirectResponseCRUDFail(request(), 'delete', "You don't have permission to delete this {$this->vars->level->name}");
                }
            }),
            'enable' => call_user_func(function() use ($model) {
                if ($this->isUnmodifiableModel($model)) {
                    return $this->redirectResponseCRUDFail(request(), 'enable', "This {$this->vars->level->name} cannot be enabled");
                }
                if (!$model->isEditableBy(Auth::user()->id)) {
                    return $this->redirectResponseCRUDFail(request(), 'enable', "You don't have permission to enable this {$this->vars->level->name}");
                }
            }),
            'disable' => call_user_func(function() use ($model) {
                if ($this->isUnmodifiableModel($model)) {
                    return $this->redirectResponseCRUDFail(request(), 'disable', "This {$this->vars->level->name} cannot be disabled");
                }
                if (!$model->isEditableBy(Auth::user()->id)) {
                    return $this->redirectResponseCRUDFail(request(), 'disable', "You don't have permission to disable this {$this->vars->level->name}");
                }
            }),
        };
        Debug::stop('permissions_check');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    public function checkModuleRelation($model)
    {
        if ($model->hasRelation('module') && (request()->input('module') > 0) &&  $module = PbModule::find(request()->input('module'))) {
            $model->module()->associate($module);
        }
        return $model;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param null $element
     * @param bool $multiple
     * @param null $id
     * @param bool $plural
     * @return array
     */
    protected function buildModelsArray(
        $element = null,
        bool $multiple = false,
        $id = null,
        bool $plural = false,
    ): array {
        $arrayElements = [];
        if ($element) {
            if ($multiple) {
                foreach ($element as $key => $value) {
                    $arrayElements[($value['size'] == 'multiple' ? $this->vars->{$key}->prefixNames : $this->vars->{$key}->prefixName)] = $value['object'];
                }
            } else {
                $arrayElements[($plural ? $this->vars->level->prefixNames : $this->vars->level->prefixName)] = $element;
            }
        } else {
            $arrayElements[($plural ?
                $this->vars->level->prefixNames :
                $this->vars->level->prefixName
            )] = ($id ?
                $this->vars->level->modelPath::find($id) :
                $this->buildPaginatedAndOrderedModel()
            );
        }

        return $arrayElements;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    protected function shareVars()
    {
        $shared = [
            ...$this->globalInertiaShare(),
            ...PbShares::allowed($this->vars->allowed),
            ...PbShares::list($this->vars->shares),
            ...['sort' => $this->vars->sortable],
            ...['showpos' => $this->vars->showPosition],
            ...['showid' => $this->vars->showId],
            ...['model' => $this->vars->viewModelName],
            ...['required' => $this->vars->required],
            ...['defaults' => $this->getDefaults()],
            ...['listing' => $this->vars->listing],
            ...['formconfig' => (isset($this->vars->config['form']) && $this->vars->config['form']) ? $this->vars->config['form'] : []],
            ...['pagination' => (isset($this->vars->pagination) && $this->vars->pagination) ? $this->vars->pagination : ['location' => 'none']],
            ...['heading' => (isset($this->vars->heading) && $this->vars->heading) ? $this->vars->heading : ['location' => 'none']],
            ...['orderby' => $this->vars->orderby ?? []],
        ];
        Inertia::share('shared', $shared);
        Debug::add($shared, 'shared');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    protected function getDefaults()
    {
        $defaults = (object)[];
        foreach ($this->vars->defaults as $key => $value) {
            $defaults->$key = match ($key) {
                'lang' => PbLanguage::findByCode($value),
                'country' => PbCountry::findByCode($value),
                default => $value,
            };
        }
        return $defaults;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    protected function getRequired()
    {
        $required = [];
        foreach ($this->vars->validationRules as $key => $value) {
            if (in_array('required', $value)) {
                array_push($required, $key);
            }
        }
        return $required;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param array $validationRules
     * @param Request $request
     * @param array $replacers
     * @param null $model
     * @return array|object
     */
    protected function processModelRequests(
        array $validationRules,
        Request $request,
        array $replacers,
        $model = null
    ): array|object
    {
        $keys = [];
        foreach ($validationRules as $vrKey => $vr) {
            if (isset($replacers[$vrKey])) {
                ${$replacers[$vrKey]} = $request[$vrKey];
                array_push($keys, $replacers[$vrKey]);
            } else {
                ${$vrKey} = $request[$vrKey];
                array_push($keys, $vrKey);
            }
        }

        if (!$model) {
            $requests = [];
            foreach ($keys as $key) {
                if (!in_array($key, $this->vars->modelExclude)) {
                    $requests[$key] = ${$key};
                }
            }
            return $requests;
        }

        foreach ($keys as $key) {
            if (!in_array($key, $this->vars->modelExclude)) {
                $model->$key = ${$key};
            }
        }

        return $model;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $view
     * @param array $elements
     * @param bool $nullable
     * @return JsonResponse|InertiaResponse
     */
    protected function renderResponse($view, array $elements = [], bool $nullable = false): JsonResponse|InertiaResponse
    {
        // If not API
        if (!isApi($this->vars->request)) {
            Debug::start('builder_controller_response_building', 'builder crud controller - response building');
            Debug::add($this->vars->cacheObjects, 'caches', true);

            Debug::measure('builder crud controller - share building', function() {
                $this->shareVars();
            });

            Inertia::setRootView($this->vars->inertiaRoot);

            Debug::stop('builder_controller_response_building');

            return Inertia::render($view, $elements);
        }

        // If API
        if ($elements || $nullable) {
            return $this->handleJSONResponse($elements, 'Operation Successful');
        } else {
            return $this->handleJSONError('Operation Failed');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $route
     * @param $type
     * @return string
     */
    protected function buildRouteString($route, $type): string
    {
        return $this->vars->{$route}->viewsPath .
            $this->buildFile(
                $type,
                ['singular' => $this->vars->{$route}->key, 'plural' => $this->vars->{$route}->keys]
            );
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $key
     * @return object
     */
    public function buildControllerVars($key): object
    {
        $object = (object)[];
        $object->key = $key;
        $object->keys = Str::plural($key);
        $object->name = strtolower($key);
        $object->names = Str::plural($object->name);
        $object->prefixName = strtolower($this->vars->helper->prefix . $key);
        $object->prefixNames = Str::plural($object->prefixName);
        $object->modelPath = $this->vars->helper->vendor . "\\" . $this->vars->helper->package . "\\Models\\" . $this->vars->helper->prefix . $key;
        $object->viewsPath = $this->vars->helper->package . "/" . $object->keys . "/";
        $object->table = resolve($object->modelPath)->getTable();
        return $object;
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $vars
     * @return void
     */
    public function varsObject($vars)
    {
        if (!isset($this->vars)) {
            $this->vars = (object)[];
        }
        foreach ($vars as $key => $value) {
            $this->vars->{$key} = $value;
        }
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return void
     */
    public function initVars()
    {
        $this->vars = ($this->vars ?? (object)[]);
        $this->vars->helper = ($this->vars->helper ?? (object)[]);
        $this->vars->helper->class =
            ($this->vars->helper->class ??
                app(PbUtilities::class)->vendor . '\\' . app(PbUtilities::class)->package . '\\Utilities\\' . app(PbUtilities::class)->prefix . 'Utilities'
            );
        $this->vars->validationRules = ($this->vars->validationRules ?? []);
        $this->vars->allowed = ($this->vars->allowed ?? []);
        $this->vars->shares = ($this->vars->shares ?? []);
        $this->vars->modelExclude = ($this->vars->modelExclude ?? []);
        $this->vars->listing = ($this->vars->listing ?? []);
        $this->vars->replacers = ($this->vars->replacers ?? []);
        $this->vars->defaults = ($this->vars->defaults ?? (object)[]);
        $this->vars->showPosition = ($this->vars->showPosition ?? false);
        $this->vars->showId = ($this->vars->showId ?? true);
        $this->vars->sortingRef = ($this->vars->sortingRef ?? "");
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return void
     */
    public function pushRequired($array)
    {
        $this->vars->required = [
            ...$this->vars->required,
            ...$array
        ];
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return void
     */
    public function pushValidationRules($array)
    {
        $this->vars->validationRules = [
            ...$this->vars->validationRules,
            ...$array
        ];
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $model
     * @return bool
     */
    public function isUndeletableModel($model): bool
    {
        if (isset($model->undeletableModels)) {
            foreach($model->undeletableModels as $key => $value) {
                if (in_array($model->{$key}, $value)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $model
     * @return bool
     */
    public function isUnmodifiableModel($model): bool
    {
        if (isset($model->unmodifiableModels)) {
            foreach($model->unmodifiableModels as $key => $value) {
                if (in_array($model->{$key}, $value)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $model
     * @return bool
     */
    public function isUnreadableModel($model): bool
    {
        if (isset($model->unreadableModels)) {
            foreach($model->unreadableModels as $key => $value) {
                if (in_array($model->{$key}, $value)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $model
     * @return bool
     */
    public function isUnconfigurableModel($model): bool
    {
        if (isset($model->unconfigurableModels)) {
            foreach($model->unconfigurableModels as $key => $value) {
                if (in_array($model->{$key}, $value)) {
                    return true;
                }
            }
        }
        return false;
    }
}
