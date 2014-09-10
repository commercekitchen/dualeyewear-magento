<?php

class Browsewire_Cmbannerslider_Model_System_Config_Source_Dropdown_Values
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'random',
                'label' => 'Random',
            ),
            array(
                'value' => 'swirl',
                'label' => 'Swirl',
            ),
            array(
                'value' => 'rain',
                'label' => 'Rain',
            ),
            array(
                'value' => 'straight',
                'label' => 'Straight',
            ),
        );
    }
}
