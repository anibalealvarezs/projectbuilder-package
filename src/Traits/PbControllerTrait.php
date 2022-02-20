<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Anibalealvarezs\Projectbuilder\Utilities\PbShares;
use Anibalealvarezs\Projectbuilder\Models\PbCurrentUser;

use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

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
     * @param int $page
     * @param int $perpage
     * @param string|null $orderby
     * @param string $field
     * @param string $order
     * @param array $defaultOrder
     * @return LengthAwarePaginator
     */
    public function paginateAndOrder(
        Builder $query,
        int $page = 1,
        int $perpage = 0,
        string $orderby = null,
        string $field = 'id',
        string $order = 'asc',
        array $defaultOrder = []
    ): LengthAwarePaginator
    {
        $config = (isset($this->vars->config) && $this->vars->config) ? $this->vars->config : $this->vars->level->modelPath::getCrudConfig(true);
        if (!$perpage && isset($config['pagination']['per_page']) && $config['pagination']['per_page']) {
            $perpage = $config['pagination']['per_page'];
        }
        if ($orderby) {
            $query->orderBy($field, $order);
        } else {
            foreach ($defaultOrder as $key => $value) {
                $query->orderBy($key, $value);
            }
        }
        return $query->paginate($perpage ?: (getConfigValue('_DEFAULT_TABLE_SIZE_') ?: 10), ['*'], 'page', $page ?: 1);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Builder|null $query
     * @param int $page
     * @param int $perpage
     * @param string|null $orderby
     * @param string $field
     * @param string $order
     * @param array $defaultOrder
     * @return LengthAwarePaginator|Collection
     */
    public function buildPaginatedAndOrderedModel(
        Builder $query = null,
        int $page = 1,
        int $perpage = 0,
        string $orderby = null,
        string $field = 'id',
        string $order = 'asc',
        array $defaultOrder = []
    ): LengthAwarePaginator|Collection
    {
        if (!isset($this->vars->level->modelPath::$sortable) || !$this->vars->level->modelPath::$sortable) {
            return $this->paginateAndOrder(
                query: $query ?: $this->vars->level->modelPath::withPublicRelations(),
                page: $page,
                perpage: $perpage,
                orderby: $orderby,
                field: $field,
                order: $order,
                defaultOrder: $defaultOrder
            );
        } else {
            return $query->get();
        }
    }
}
