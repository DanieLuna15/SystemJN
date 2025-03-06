<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DateRangePicker extends Component
{
    public $name;
    public $label;
    public $config;
    public function __construct($name, $label = 'Rango de Fecha/Hora', $config = [])
    {
        $this->name = $name;
        $this->label = $label;
        $this->config = array_merge([
            'showDropdowns' => true,
            'startDate' => 'js:moment().startOf("month")',
            'endDate' => 'js:moment().endOf("month")',
            'minYear' => 2000,
            'maxYear' => "js:parseInt(moment().format('YYYY'),10)",
            'timePicker' => true,
            'timePicker24Hour' => true,
            'timePickerSeconds' => true,
            'locale' => [
                'format' => 'DD-MM-YYYY HH:mm:ss',
                'separator' => ' - ',
                'applyLabel' => 'Aplicar',
                'cancelLabel' => 'Cancelar',
                'fromLabel' => 'Desde',
                'toLabel' => 'Hasta',
                'customRangeLabel' => 'Personalizado',
                'weekLabel' => 'S',
                'daysOfWeek' => ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                'monthNames' => [
                    'Enero',
                    'Febrero',
                    'Marzo',
                    'Abril',
                    'Mayo',
                    'Junio',
                    'Julio',
                    'Agosto',
                    'Septiembre',
                    'Octubre',
                    'Noviembre',
                    'Diciembre'
                ],
                'firstDay' => 1
            ],
            'cancelButtonClasses' => 'btn-danger',
            'opens' => 'center',
        ], $config);
    }

    public function render()
    {
        return view('components.date-range-picker');
    }
}
