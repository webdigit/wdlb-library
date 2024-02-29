<?php
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

/**
 * Gère les catégories.
 *
 * Cette fonction est responsable de la gestion des catégories dans le plugin.
 * Elle permet d'ajouter, modifier et supprimer des catégories, ainsi que d'afficher la liste des catégories existantes.
 * Le formulaire d'ajout ou de modification d'une catégorie est affiché, suivi d'un tableau contenant les catégories existantes.
 *
 * @return void
 */
function wdlb_manage_categories() {
    if (isset($_POST['wdlb_submit_category'])) {
        if ($_POST['wdlb_action'] === 'add') {
            wdlb_add_categories($_POST);
        } elseif ($_POST['wdlb_action'] === 'edit') {
            wdlb_edit_category($_POST);
        }
    }

    if (isset($_GET['delete'])) {
        wdlb_delete_category(intval($_GET['delete']));
    }

    $categories = wdlb_get_all_categories();

    $category_to_edit = null;
    if (isset($_GET['edit'])) {
        $category_to_edit = wdlb_get_category(intval($_GET['edit']));
    }
    ?>
    <div class="wdlb-container">
        <div class="wrapper">
            <h2>Gérer les catégories</h2>
            
            <!-- Formulaire pour ajouter ou modifier une catégorie -->
            <form method="post" class="wdlb-form">
                <?php wp_nonce_field( 'wdlb_manage_categories_action', 'wdlb_manage_categories_nonce' ); ?>
                <input type="hidden" name="wdlb_action" value="<?php echo isset($category_to_edit) ? 'edit' : 'add'; ?>">
                <input type="hidden" name="category_id" value="<?php echo isset($category_to_edit) ? $category_to_edit->id : ''; ?>">
                <div class="input-wrapper">
                    <label>Nom de la catégorie:</label>
                    <input type="text" name="category_name" value="<?php echo isset($category_to_edit) ? $category_to_edit->category_name : ''; ?>" required>
                    <label>Lien e-mail:</label>
                    <input type="text" name="email_link" placeholder="<?php _e('Séparez les email par ;', 'webdigit-library') ?>" value="<?php echo isset($category_to_edit) ? $category_to_edit->email_link : ''; ?>">
                </div>
                <div class="input-wrapper">
                    <label for="image_url" style="display:none;">URL de l'image:</label>
                    <input type="text" id="image_url" value="<?php echo isset($category_to_edit) ? $category_to_edit->image_url : ''; ?>" name="image_url" style="display:none;">
                    <input type="hidden" id="image_id" name="image_id"><br>
                    <a href="#" id="select_image">Sélectionner une image</a><br>
                    <div id="image_preview"><?php if (isset($category_to_edit) && $category_to_edit->image_url): ?><img src="<?php echo $category_to_edit->image_url; ?>" width="50" height="50" alt=""><?php endif; ?></div><br>
                    <input type="submit" name="wdlb_submit_category" value="<?php echo isset($category_to_edit) ? 'Enregistrer les modifications' : 'Ajouter la catégorie'; ?>">
                    <?php if (isset($category_to_edit)) : ?>
                        <a href="<?php echo admin_url('admin.php?page=wdlb_categories'); ?>" class="button">Annuler</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
        <!-- Tableau pour afficher la liste des catégories -->
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Image</th>
                    <th>Email</th>
                    <th>Date de création</th>
                    <th>Actions</th> <!-- Nouvelle colonne pour les actions -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category) : ?>
                    <tr>
                        <td><?php echo $category->category_name; ?></td>
                        <td><?php if ($category->image_url): ?><img src="<?php echo $category->image_url; ?>" width="50" height="50" alt=""><?php endif; ?></td>
                        <td><?php echo $category->email_link; ?></td>
                        <td><?php echo $category->created_at; ?></td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=wdlb_categories&delete=' . $category->id ); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                                <span class="dashicons dashicons-trash"></span> <!-- Icône de suppression -->
                            </a>
                            <a href="<?php echo admin_url( 'admin.php?page=wdlb_categories&edit=' . $category->id ); ?>">
                                <span class="dashicons dashicons-edit"></span> <!-- Icône d'édition -->
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php
}

/**
 * Adds categories to the library.
 *
 * @param array $datas The data containing the category information.
 * @return void
 */
function wdlb_add_categories($datas) {
    $categories_manager = new WDLB_Categories();

    if ( ! isset( $datas['wdlb_manage_categories_nonce'] ) || ! wp_verify_nonce( $datas['wdlb_manage_categories_nonce'], 'wdlb_manage_categories_action' ) ) {
        wp_die( 'Nonce non valide' );
    }

    $category_name = sanitize_text_field($datas['category_name']);
    $image_url = esc_url($datas['image_url']);
    $email_link = sanitize_email($datas['email_link']);

    $categories_manager->insert_categories(
        array(
            'category_name' => $category_name,
            'image_url' => $image_url,
            'email_link' => $email_link
        )
    );
}

/**
 * Updates a category with the provided data.
 *
 * @param array $datas The data to update the category.
 * @return void
 */
function wdlb_edit_category($datas) {
    $categories_manager = new WDLB_Categories();

    if ( ! isset( $datas['wdlb_manage_categories_nonce'] ) || ! wp_verify_nonce( $datas['wdlb_manage_categories_nonce'], 'wdlb_manage_categories_action' ) ) {
        wp_die( 'Nonce non valide' );
    }

    $category_id = intval($datas['category_id']);
    $category_name = sanitize_text_field($datas['category_name']);
    $email_link = sanitize_email($datas['email_link']);

    if(isset($datas['image_url']) && !empty($datas['image_url'])) {
        $image_url = esc_url($datas['image_url']);
    } else {
        $category = wdlb_get_category($category_id);
        $image_url = $category->image_url;
    }

    $categories_manager->update_category(
        array(
            'id' => $category_id,
            'category_name' => $category_name,
            'image_url' => $image_url,
            'email_link' => $email_link
        )
    );
}

/**
 * Deletes a category by its ID.
 *
 * @param int $id The ID of the category to delete.
 * @return void
 */
function wdlb_delete_category($id) {
    $categories_manager = new WDLB_Categories();
    $categories_manager->delete_category($id);
}

/**
 * Retrieves all categories from the WDLB_Categories class.
 *
 * @return array The array of categories.
 */
function wdlb_get_all_categories() {
    $categories_manager = new WDLB_Categories();
    return $categories_manager->get_categories();
}

/**
 * Retrieves a category by its ID.
 *
 * @param int $id The ID of the category to retrieve.
 * @return mixed The category object if found, null otherwise.
 */
function wdlb_get_category($id) {
    $categories_manager = new WDLB_Categories();
    return $categories_manager->get_category($id);
}
