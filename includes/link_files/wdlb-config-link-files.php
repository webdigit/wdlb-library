<?php

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

function wdlb_manage_linked_files() {
    if (isset($_POST['wdlb_submit_file'])) {
        if ($_POST['wdlb_action'] === 'add') {
            wdlb_add_linked_file($_POST);
        } elseif ($_POST['wdlb_action'] === 'edit') {
            wdlb_edit_linked_file($_POST);
        }
    }

    if (isset($_GET['delete'])) {
        wdlb_delete_linked_file(intval($_GET['delete']));
    }

    $files = wdlb_get_all_files();
    $categories = wdlb_get_categories();

    // Récupérer les données du fichier lié à éditer
    $file_to_edit = null;
    if (isset($_GET['edit'])) {
        $file_to_edit = wdlb_get_linked_file(intval($_GET['edit']));
        $file_to_edit->category_id = unserialize($file_to_edit->category_id);
    }

    ?>
    <div class="wdlb-container">
        <div class="wrapper">
            <h2><?php _e( 'Manage library content', 'webdigit-library' ); ?></h2>

            <!-- Formulaire pour ajouter ou modifier un fichier lié -->
            <form method="post" class="wdlb-form">
                <?php wp_nonce_field( 'wdlb_manage_linked_files_action', 'wdlb_manage_linked_files_nonce' ); ?>
                <input type="hidden" name="wdlb_action" value="<?php echo isset($file_to_edit) ? 'edit' : 'add'; ?>">
                <input type="hidden" name="file_id" value="<?php echo isset($file_to_edit) ? $file_to_edit->id : ''; ?>">
                <input type="hidden" id="document_id" name="post_id" value="<?php echo isset($file_to_edit) ? $file_to_edit->post_id : ''; ?>">
                <div class="input-wrapper">
                    <label><?php _e( 'Name', 'webdigit-library' ); ?></label>
                    <input type="text" name="name" value="<?php echo isset($file_to_edit) ? stripslashes($file_to_edit->name) : ''; ?>" required><br>
                    <label><?php _e( 'Description', 'webdigit-library' ); ?></label>
                    <input type="text" name="desc_text" value="<?php echo isset($file_to_edit) ? stripslashes($file_to_edit->desc_text) : ''; ?>"><br>
                </div>
                <div class="input-wrapper">
                    <label><?php _e( 'Categories', 'webdigit-library' ); ?></label><br>
                    <?php foreach ($categories as $category) : ?>
                        <?php
                            $checked = false;
                            if ( isset($file_to_edit) && ($file_to_edit->category_id &&isset($file_to_edit->category_id))) {
                                foreach ($file_to_edit->category_id as $cat_id) {
                                    if ($cat_id == $category->id) {
                                        $checked = true;
                                        break;
                                    }
                                }
                            }
                        ?>
                        <label>
                            <input type="checkbox" name="category_id[]" value="<?php echo $category->id; ?>" <?php echo $checked ? 'checked' : ''; ?>>
                            <?php echo $category->category_name; ?>
                        </label><br>
                    <?php endforeach; ?>

                    <br>
                </div>
                <div class="input-wrapper">
                    <label for="image_url"><?php _e( 'Cover image', 'webdigit-library' ); ?></label>
                    <input type="text" id="image_url" name="img_couv" value="<?php echo isset($file_to_edit) ? $file_to_edit->img_couv : ''; ?>" style="display:none;">
                    <a href="#" id="select_image"><?php _e( 'Select a cover image', 'webdigit-library' ); ?></a>
                    <div id="image_preview">
                        <?php if (isset($file_to_edit) && $file_to_edit->img_couv): ?>
                            <img src="<?php echo $file_to_edit->img_couv; ?>" width="50" height="50" alt="">
                        <?php endif; ?>
                    </div><br>

                    <div id="wdlb-content-upload-wrapper">
                        <div id="toggleLinkField">
                            <label><?php _e( 'Link url', 'webdigit-library' ); ?></label>
                            <input type="text" id="link" name="link" value="<?php echo isset($file_to_edit) ? $file_to_edit->link : ''; ?>"><br>
                        </div>

                        <div id="toggleDocField">
                            <label for="document_url"><?php _e( 'Ressources', 'webdigit-library' ); ?></label>
                            <input type="hidden" id="document_url" name="document_url" value="<?php echo isset($file_to_edit) ? $file_to_edit->document_url : ''; ?>">
                            <a href="#" id="select_document_url"><?php _e( 'Select a ressource', 'webdigit-library' ); ?></a>
                            <div id="document_url_preview">
			                    <?php if (isset($file_to_edit) && $file_to_edit->document_url): ?>
				                    <?php $thumbnail = str_replace('.pdf', '-pdf.jpg', $file_to_edit->document_url); ?>
                                    <a href="<?php echo $file_to_edit->document_url; ?>" target="_blank"><img src="<?php echo $thumbnail; ?>" width="50" height="50" alt=""></a>
			                    <?php endif; ?>
                            </div><br>
                        </div>
                        <div>or</div>
                        <button type="button" id="toggleFields" class="button wdlb-or-another-content">Encoder un lien</button>
                    </div>
                    <input type="submit" name="wdlb_submit_file" value="<?php echo isset($file_to_edit) ? 'Enregistrer les modifications' : 'Ajouter le fichier'; ?>">
                    <?php if (isset($file_to_edit)) : ?>
                        <a href="<?php echo admin_url('admin.php?page=wdlb'); ?>" class="button">Annuler</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

        <!-- Tableau pour afficher la liste des fichiers liés -->
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e( 'Name', 'webdigit-library' ); ?></th>
                    <th><?php _e( 'Description', 'webdigit-library' ); ?></th>
                    <th><?php _e( 'Categories', 'webdigit-library' ); ?></th>
                    <th><?php _e( 'Cover image', 'webdigit-library' ); ?></th>
                    <th><?php _e( 'Ressource', 'webdigit-library' ); ?></th>
                    <th><?php _e( 'Created at', 'webdigit-library' ); ?></th>
                    <th><?php _e( 'Actions', 'webdigit-library' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file) : ?>
                    <tr>
                        <td><?php echo stripslashes($file->name); ?></td>
                        <td><?php echo stripslashes($file->desc_text); ?></td>
                        <td><?php echo wdlb_get_category_names($file->category_id); ?></td>
                        <td><?php if ($file->img_couv): ?><img src="<?php echo $file->img_couv; ?>" width="50" height="50" alt=""><?php endif; ?></td>
                        <td><?php if ($file->document_url || $file->link): ?>
                                <?php $ressource_url = isset($file->document_url) && strlen($file->document_url) ? $file->document_url : $file->link; ?>
                                
                                <a href="<?php echo $ressource_url; ?>" target="_blank">Voir la ressource <?php if(isset($file->document_url) && strlen($file->document_url) ): ?> (file) <?php else: ?> (link) <?php endif; ?></a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $file->created_at; ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=wdlb&delete=' . $file->id ); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?');">
                                <span class="dashicons dashicons-trash"></span> 
                            </a>
                            <a href="<?php echo admin_url( 'admin.php?page=wdlb&edit=' . $file->id ); ?>">
                                <span class="dashicons dashicons-edit"></span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php
}

function wdlb_get_linked_file($id) {
    $linked_files_manager = new WDLB_Linkfiles();
    return $linked_files_manager->get_link_files($id);
}

function wdlb_get_all_files() {
    $linked_files_manager = new WDLB_Linkfiles();
    return $linked_files_manager->get_all_link_files();
}

function wdlb_add_linked_file($datas) {
    $linked_files_manager = new WDLB_Linkfiles();
    $linked_files_manager->insert_link_files($datas);
}

function wdlb_edit_linked_file($datas) {
    $linked_files_manager = new WDLB_Linkfiles();
    $linked_files_manager->edit_link_files($datas);
}

function wdlb_delete_linked_file($id) {
    $linked_files_manager = new WDLB_Linkfiles();
    $linked_files_manager->delete_link_files($id);
}

function wdlb_get_categories() {
    $categories_manager = new WDLB_Categories();
    return $categories_manager->get_categories();
}

function wdlb_get_category_names($category_ids) {
    $category_ids = unserialize($category_ids);

    if (!is_array($category_ids)) {
        return '';
    }

    $category_names = array();
    $categories_manager = new WDLB_Categories();
    foreach ($category_ids as $category_id) {
        $cat = $categories_manager->get_category($category_id);
        if (!$cat) {
            continue;
        }
        $category_names[] = $cat->category_name;
    }
    return implode(', ', $category_names);
}
