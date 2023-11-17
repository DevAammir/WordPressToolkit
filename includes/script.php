<script>
    admin_ajax_url = "<?php echo WPT_AJAX; ?>";


    (function($) {
        $(document).ready(function() {
            console.log('Welcome to WordPress Toolkit!');

            <?php $page = WPT_URL . 'includes/page.php'; ?>

            $(document).on('click', '#wpt_save_settings', function(e) {
                e.preventDefault();
                let form_data = $('#wpt_save_settings_form').serialize();
                console.log(form_data);
                _AJAX_function_1('#target', admin_ajax_url, 'wpt_save_settings', 'POST', form_data, 'json');
                if ($('#wpt_enable_social_media').prop('checked')) {
                    $('#social_media_tab').show();
                    $('#social_media_tab_content').show();
                } else {
                    $('#social_media_tab').hide();
                    $('#social_media_tab_content').hide();
                }
            });


        });
    })(jQuery);
    //TABS
    function showContent(tabId, element) {
        // Hide all content
        var contents = document.getElementsByClassName('content');
        for (var i = 0; i < contents.length; i++) {
            contents[i].classList.remove('active');
        }

        // Show the selected tab's content
        document.getElementById(tabId).classList.add('active');

        // Remove 'active' class from all tabs
        var tabs = document.getElementsByClassName('tab');
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].classList.remove('active');
        }

        // Add 'active' class to the clicked tab
        element.classList.add('active');
    }
</script>
<style>
    /**TABS CSS**/
    .tabs {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .tab {
        margin-right: 10px;
        padding: 10px;
        background-color: #ddd !important;
        cursor: pointer;
        border: 1px solid #BBB;
        color: #777;
    }

    .tab:hover,
    .tab.active {
        background-color: #CCC !important;
        border-color: #AAA !important;
        color: #000;
    }

    .content {
        display: none;
    }

    .content.active {
        display: block;
        min-height: 500px;
        background: #EDEDED;
        padding: 20px;
        border: 1px solid #DDD;
    }

    /**PAGE CSS**/
    .form_builder_row {
        margin: 20px 0;
    }

    .form_builder_row>label {
        width: 300px !important;
        display: block;
        float: left;
        font-size: 16px;
        font-weight: 400;
    }

    .wrap {
        padding: 10px 20px;
    }

    .wrap h2 {
        margin-top: 30px;
        margin-bottom: 20px;
    }

    .hide {
        display: none !important;
    }

    div#loader {
        display: table-cell;
        position: absolute;
        background: rgba(33, 33, 33, 0.5);
        color: #FFF;
        padding: 20px;
        top: 15%;
        left: 1%;
        font-size: 50px;
        width: 1100px;
        height: 35rem;
        text-align: center;
        border: 2px dotted #EEE;
        line-height: 550px;
    }
</style>