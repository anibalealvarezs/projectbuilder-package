<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Illuminate\Support\Facades\Schema;

trait PbControllerListingTrait
{
    protected static $item = "";
    protected static $route = "";

    /*
     * Build default listing items.
     *
     * @var array
     */
    protected static function buildListingRow($config, $default = []): array
    {
        // Push default fields
        $row = self::buildDefaultFields($default);
        // Push custom fields
        $fields = array_merge(Schema::getColumnListing((new $config['model'])->getTable()), $config['relations']);
        $fields = array_merge(array_intersect($config['custom_order'], $fields), array_diff($fields, $config['custom_order']));
        foreach ($fields as $field) {
            if (in_array($field, array_keys($config['fields']))) {
                $config['fields'][$field]['key'] = $field;
                $row[] = self::buildListingField($config['fields'][$field]);
            }
        }
        // Push actions
        $row[] = self::buildActionsField($config['fields']['actions'], $config['action_routes'], $config['enabled_actions']);

        return $row;
    }

    /*
     * Build default listing items.
     *
     * @var array
     */
    protected static function buildDefaultFields($options = []): array
    {
        $items = [];

        if (!$options) {
            $options = [
                'showid' => true,
                'sort' => false
            ];
        }

        if ($options['sort']) {
            $items[] =
                self::buildListingField(
                    [
                        'key' => 'sorthandle',
                        'name' => "Sort",
                        'style' => [
                            'centered' => true,
                            'bold' => true,
                            'width' => "w-12",
                        ],
                    ]
                );
        }

        if ($options['showid']) {
            $items[] =
                self::buildListingField(
                    [
                        'key' => "item",
                        'name' => "#",
                        'style' => [
                            'centered' => true,
                            'bold' => true,
                            'width' => "w-12",
                        ],
                    ]
                );
        }

        return $items;
    }

    /*
     * Default listing data.
     *
     * @var array
     */
    protected static function buildActionsField($actions = [], $actionRoutes = [], $enabledActions = []): array
    {
        return self::buildListingField(
            [
                'key' => "actions",
                'style' => [
                    'centered' => true,
                    'bold' => false,
                    'width' => "w-20",
                ],
                'buttons' => self::buildActions($actions, $actionRoutes, $enabledActions),
            ]
        );
    }

    /*
     * Default listing item configuration.
     *
     * @var array
     */
    protected static function buildListingField($options = []): array
    {

        /* Style */
        if (!isset($options['style']['centered'])) {
            $options['style']['centered'] = false;
        }
        if (!isset($options['style']['bold'])) {
            $options['style']['bold'] = false;
        }
        if (!isset($options['style']['width'])) {
            $options['style']['width'] = "";
        }
        /* Buttons */
        /* Href */
        if (!isset($options['href']['route']) || !isset($options['href']['id'])) {
            $options['href'] = [];
        }
        /* Buttons */
        /* Href */
        if (!isset($options['href']['route']) || !isset($options['href']['id'])) {
            $options['href'] = [];
        }
        // Status
        if (!isset($options['status'])) {
            $options['status'] = false;
        }

        return [
            "key" => ($options['key'] ?? ""),
            "name" => ($options['name'] ?? ($options['key'] ? ucwords($options['key']) : "")),
            "arrval" => ($options['arrval'] ?? []),
            "style" => ($options['style'] ?? []),
            "buttons" => ($options['buttons'] ?? []),
            "href" => ($options['href'] ?? []),
            "size" => ($options['size'] ?? 'single'),
            "status" => ($options['status'] ?? false),
        ];
    }

    /*
     * Default listing item configuration.
     *
     * @var array
     */
    protected static function buildActions($actions, $actionRoutes, $enabledActions): array
    {
        foreach ($actions as $key => &$action) {
            if ($enabledActions[$key]) {
                /* Text */
                if (!isset($action['text'])) {
                    $action['text'] = ($key == 'update' ? 'Edit' : ($key == 'delete' ? 'Delete' : 'NO TEXT DEFINED'));
                }
                /* ID */
                /* Text */
                if (!isset($action['id'])) {
                    $action['id'] = true;
                }
                /* Route */
                if (!isset($action['route'])) {
                    $action['route'] = ($key == 'update' ? self::$route.'.edit' : ($key == 'delete' ? self::$route.'.destroy' : '/'));
                }
                /* Callback */
                if (!isset($action['callback'])) {
                    $action['callback'] = "";
                }
                /* Form item name */
                if (!isset($action['formitem'])) {
                    $action['formitem'] = self::$item;
                }
                /* Style */
                if (!isset($action['style'])) {
                    $action['style'] = ($key == 'update' ? 'secondary' : ($key == 'delete' ? 'danger' : 'default'));
                }
                /* Type */
                if (!isset($action['type'])) {
                    $action['type'] = 'form';
                }
                /* Method */
                if (!isset($action['method'])) {
                    $action['method'] = ($key == 'update' ? 'PUT' : 'DELETE');
                }
                /* Alternative for user */
                if (!isset($action['altformodel'])) {
                    $action['altformodel'] =
                        ($key == 'update' ?
                            ($actionRoutes['update'] ?? []) :
                            ($actionRoutes['delete'] ?? [])
                        );
                }
            }
        }

        return $actions;
    }
}
