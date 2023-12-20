<?php
foreach (WPT_AVAILABLE_OPTIONS as $key => $value) {
    FORMBUILDER->field([
        'type' => 'checkbox',
        'label' => $value,
        'name' => $key,
        'id' => $key,
        'dbval' => !empty(WPT_SETTINGS[$key]) ? WPT_SETTINGS[$key] : '',
    ]);
}
echo '<hr>';

