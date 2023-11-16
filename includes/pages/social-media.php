    <?php
    foreach (AVAILABLE_SOCIAL_MEDIA_PLATFORMS as $key => $value) :

        FORMBUILDER->field([
            'type' => 'text',
            'label' => $key,
            'name' => 'sm_' . $value,
            'id' => 'sm_' . $value,
            'dbval' => !empty(WPT_SOCIALMEDIA['sm_'.$value]) ? WPT_SOCIALMEDIA['sm_'.$value] : '',
        ]);

    endforeach;

    ?>
