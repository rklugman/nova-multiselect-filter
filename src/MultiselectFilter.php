<?php declare(strict_types=1);


namespace OptimistDigtal\NovaMultiselectFilter;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

abstract class MultiselectFilter extends Filter
{
    public $component = 'nova-multiselect-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param Request $request
     * @param Builder $query
     * @param $value
     * @return Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query;
    }

    /**
     * Get the filter's options.
     *
     * @param Request $request
     * @return array|callable
     */
    public function options(Request $request)
    {
        return [];
    }

    /**
     * Sets the placeholder value displayed on the field.
     *
     * @param $placeholder
     * @return \OptimistDigtal\NovaMultiselectFilter\MultiselectFilter
     */
    public function placeholder($placeholder)
    {
        return $this->withMeta(['placeholder' => $placeholder]);
    }

    /**
     * Sets the max number of options the user can select.
     *
     * @param $placeholder
     * @return \OptimistDigtal\NovaMultiselectFilter\MultiselectFilter
     */
    public function max($max)
    {
        return $this->withMeta(['max' => $max]);
    }

    /**
     * Enables the field to be used as a single select.
     *
     * This forces the value saved to be a single value and not an array.
     *
     * @param bool $singleSelect
     * @return \OptimistDigtal\NovaMultiselectFilter\MultiselectFilter
     **/
    public function singleSelect($singleSelect = true)
    {
        return $this->withMeta(['singleSelect' => $singleSelect]);
    }

    /**
     * Sets the maximum number of options displayed at once.
     *
     * @param $optionsLimit
     * @return \OptimistDigtal\NovaMultiselectFilter\MultiselectFilter
     */
    public function optionsLimit($optionsLimit)
    {
        return $this->withMeta(['optionsLimit' => $optionsLimit]);
    }

    /**
     * Enables vue-multiselect's group-select feature which allows the
     * user to select the whole group at once.
     *
     * @param bool $groupSelect
     * @return \OptimistDigtal\NovaMultiselectFilter\MultiselectFilter
     */
    public function groupSelect($groupSelect = true)
    {
        return $this->withMeta(['groupSelect' => $groupSelect]);
    }

    /**
     * Formats the options available for select.
     *
     * @param array|callable
     * @return \OptimistDigtal\NovaMultiselectFilter\MultiselectFilter
     **/
    public function getFormattedOptions($container, $request)
    {
        if (is_callable($this->options($container->make($request))))
            $options = call_user_func($this->options($container->make($request)));

        $options = collect($this->options($container->make($request)) ?? []);

        $isOptionGroup = $options->contains(function ($label, $value) {
            return is_array($label);
        });

        if ($isOptionGroup) {
            return $options
                ->map(function ($value, $key) {
                    return collect($value + ['value' => $key]);
                })
                ->groupBy('group')
                ->map(function ($value, $key) {
                    return ['label' => $key, 'values' => $value->map->only(['label', 'value'])->toArray()];
                })
                ->values()
                ->toArray();
        }

        return $options->map(function ($label, $value) {
            return ['label' => $label, 'value' => $value];
        })->values()->all();
    }

    /**
     * Prepare the filter for JSON serialization.
     *
     * @return array
     */
    public
    function jsonSerialize()
    {
        return array_merge([
            'class' => $this->key(),
            'name' => $this->name(),
            'component' => $this->component(),
            'options' => $this->getFormattedOptions(Container::getInstance(), Request::class),
            'currentValue' => $this->default() ?? '',
        ], $this->meta());
    }
}