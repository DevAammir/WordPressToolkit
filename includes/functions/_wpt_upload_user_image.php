<?php 



/**
 * Uploads a user image to the server.
 *
 * @param array $params An array containing the user identifier and the image data.
 *                     - user_identifier: The identifier of the user (either an ID or a username).
 *                     - image: The image data to be uploaded.
 * @return string|false The URL of the uploaded image on success, or false on failure.
 * @additional  can test with this shortcode [image_upload_test] 
 * NOTE that this only processes the image and saves to the database the URL of the image. 
 */
function _wpt_upload_user_image($params)
{
    if($params == 'help'){_wpt_upload_user_image_help();die();}
    $user_identifier = $params['user_identifier'];
    $image = $params['image'];

    // Use wp_upload_dir() to get the correct upload directory
    $upload_dir = wp_upload_dir();
    $upload_directory = $upload_dir['basedir'] . '/users/';

    if (is_numeric($user_identifier)) {
        $user = get_user_by('id', $user_identifier);
    } else {
        // If $user_identifier is not numeric, assume it's a username
        $user = get_user_by('login', $user_identifier);
    }

    // Ensure the directory exists or create it
    if (!file_exists($upload_directory)) {
        mkdir($upload_directory, 0777, true);
    }

    $image_extension = pathinfo($image['name'], PATHINFO_EXTENSION);

    $new_image_name = $user->user_login . '_' . uniqid() . '.' . $image_extension;

    // Set the full path for the new image
    $new_image_path = $upload_directory . $new_image_name;

    // Check if the file was successfully moved
    if (move_uploaded_file($image['tmp_name'], $new_image_path)) {
        $full_image_url = $upload_dir['baseurl'] . '/users/' . $new_image_name;
        update_user_meta($user->ID, 'wpt_profile_image', $full_image_url);
        return 1;
    } else {
        // Handle upload error
        return "Error uploading image.";
    }
}





add_shortcode('image_upload_test', 'image_upload_test_cb');
function image_upload_test_cb()
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["user_name"]) && isset($_FILES["user_profile_image"])) {
            $user_name = $_POST["user_name"];
            $image = $_FILES['user_profile_image'];

            // Call the uploadImage function
            $result = _wpt_upload_user_image(['user_identifier' => 'joe', 'image' => $image]);

            echo $result;
        } else {
            echo "Please provide both user name and image.";
        }
    }
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="user_name">User Name:</label>
        <input type="text" name="user_name"><br>

        <label for="image">Choose Image:</label>
        <input type="file" name="user_profile_image" accept="image/*"><br>

        <input type="submit" value="Upload Image">
    </form>
    <?php
}




function _wpt_upload_user_image_help()
{
?>
  <h3>_wpt_upload_user_image() help</h3>
  <code>
    $params = [
      'user_identifier' => 'user_id_or_username',
      'image'           => 'image_data'
    ];<br/><br/>
    _wpt_upload_user_image($params);<br/>
  </code><br/><br/>
  <p>Uploads a user image to the server.</p>

  <p><strong>Parameters:</strong></p>
  <ul>
    <li><code>'user_identifier'</code> (string) - The identifier of the user (either an ID or a username).</li>
    <li><code>'image'</code> (string) - The image data to be uploaded.</li>
  </ul>

  <p><strong>Returns:</strong></p>
  <p>The URL of the uploaded image on success, or false on failure.</p>

  <p><strong>Additional:</strong></p>
  <p>Can test with this shortcode [image_upload_test].</p>
  <p>NOTE that this only processes the image and saves to the database the URL of the image.</p>
<?php
}
