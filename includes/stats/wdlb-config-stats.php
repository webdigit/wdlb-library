<?php
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

/**
 * Display the statistics table.
 *
 * This function retrieves all the statistics and displays them in a table format.
 * If there are no statistics available, it displays a message indicating that there are no stats.
 */
function wdlb_display_stats () {
    $stats = wdlb_get_all_stats();
 ?>
    <div class="wdlb-container">
        <div class="wrapper">
            <h2><?php _e( 'Request Log', 'webdigit-library' ); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e( 'Requested Ressources', 'webdigit-library' ); ?></th>
                            <th><?php _e( 'Name', 'webdigit-library' ); ?></th>
                            <th><?php _e( 'Surname', 'webdigit-library' ); ?></th>
                            <th><?php _e( 'Email', 'webdigit-library' ); ?></th>
                            <th><?php _e( 'Request date', 'webdigit-library' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats)) : ?>
                            <tr>
                                <td colspan="3" style="text-align: center;"> <?php _e( 'No logs', 'webdigit-library' ); ?> </td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($stats as $stat) : ?>
                            <tr>
                                <td><?php echo $stat->ressource_name; ?><span class="wdlb_admin_document_link"><a target="_blank" href="<?php echo $stat->ressource_link;  ?>">link</a></span></td>
                                <td><?php echo $stat->name; ?></td>
                                <td><?php echo $stat->surname; ?></td>
                                <td><?php echo $stat->email; ?></td>
                                <td><?php echo $stat->requestDate; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
        </div>
    </div>
 <?php
}

/**
 * Retrieves all the statistics from the WDLB_Stats class.
 *
 * @return array An array containing all the statistics.
 */
function wdlb_get_all_stats() {
    $stats_manager = new WDLB_Stats();
    return $stats_manager->get_all_stats();
}
