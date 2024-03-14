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
            <h2>Gérer les catégories</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Ressource name</th>
                            <th>Email</th>
                            <th>Request Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats)) : ?>
                            <tr>
                                <td colspan="3" style="text-align: center;"> No Stats </td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($stats as $stat) : ?>
                            <tr>
                                <td><?php echo $stat->ressource_name; ?></td>
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
