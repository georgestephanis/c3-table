<?php
/**
 * Plugin Name: Clever Company Comparison Tables
 * Description: Providing an ACF Block for comparing assorted companies.
 * Version: 0.0.1
 * Author: George Stephanis
 * Text Domain: c3-table
 */

namespace Clever\C3Tables;

if ( ! defined( 'ABSPATH' ) ) {
    // Gotta go for the classics.
    wp_die( __( 'Cheatin’ uh?', 'c3-table' ) );
}

require_once __DIR__ . '/includes/acf-fields.php';
require_once __DIR__ . '/includes/company-post-type.php';

/**
 * Register our C3 Table block.
 *
 * @return void
 */
function register_c3_table_block() {
    // For simplicity, we're keeping `block.json` here in the plugin root.
    register_block_type( __DIR__ );
}
add_action( 'acf/init', __NAMESPACE__ . '\register_c3_table_block' );

/**
 * Render our table block.
 *
 * @return void
 */
function render_c3_table_block() {
    add_action( 'wp_footer', __NAMESPACE__ . '\render_dialog' );

    $column_titles = get_field( 'column_titles' );
    $companies = get_field( 'companies' );
    if ( empty( $companies ) ) {
        echo '<!-- No companies selected. -->';
        return;
    }
    $additional_detail_fields = get_field( 'additional_detail_fields' );
    ?>
    <div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
        <table class="c3-table">
            <thead>
                <tr>
                    <th scope="col"><?php echo esc_html( $column_titles['company'] ); ?></th>
                    <th scope="col" colspan="2"><?php echo esc_html( $column_titles['details'] ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $companies as $company ) :
                    $company_name = get_the_title( $company );
                    $company_fields = get_fields( $company );
                    $company_link = null;
                    if ( $company_fields['website_url'] && wp_http_validate_url( $company_fields['website_url'] ) ) {
                        $company_link = add_query_arg(
                            array(
                                'utm_source' => 'clever',
                                'utm_medium' => 'referral',
                                'utm_campaign' => 'company-comparisons',
                            ),
                            $company_fields['website_url']
                        );
                    }
                    ?>
                    <tr data-company-id="<?php echo esc_attr( $company_fields['company_id'] ); ?>">
                        <th class="company" scope="row">
                            <?php if ( $company_link ) : ?>
                                <a href="<?php echo esc_url( $company_link ); ?>" class="company-link">
                            <?php endif; ?>
                                <?php
                                if ( wp_http_validate_url( $company_fields['logo_url'] ) ) {
                                    printf(
                                        '<img src="%1$s" alt="%2$s" />',
                                        esc_url( $company_fields['logo_url'] ),
                                        esc_attr( $company_name )
                                    );
                                } else {
                                    printf( '<h4 class="company-name">%s</h1>', esc_html( $company_name ) );
                                }
                                ?>
                            <?php if ( $company_link ) : ?>
                                </a>
                            <?php endif; ?>

                            <div class="company-rating">
                                <span class="star-rating" style="--rating:<?php echo esc_attr( $company_fields['avg_rating'] ?? 0 ); ?>;"></span>
                                <?php if ( ! empty( $company_fields['avg_rating'] ) ) : ?>
                                    <span class="text-rating"><?php echo esc_html( $company_fields['avg_rating'] ); ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if ( ! empty( $company_fields['total_reviews'] ) ) : ?>
                                <small class="num_reviews">
                                    <?php if ( ! empty( $company_fields['review_link'] ) && wp_http_validate_url( $company_fields['review_link'] ) ) : ?>
                                        <a href="<?php echo esc_url( $company_fields['review_link'] ); ?>">
                                    <?php endif; ?>

                                    <?php
                                    echo esc_html(
                                        sprintf(
                                            _n(
                                                'Based on %s review',
                                                'Based on %s reviews',
                                                $company_fields['total_reviews'],
                                                'c3-table'
                                            ),
                                            number_format_i18n( $company_fields['total_reviews'] )
                                        )
                                    );
                                    ?>

                                    <?php if ( ! empty( $company_fields['review_link'] ) && wp_http_validate_url( $company_fields['review_link'] ) ) : ?>
                                        ≫</a>
                                    <?php endif; ?>
                                </small>
                            <?php endif; ?>
                        </th>
                        <td class="details">
                            <dl>
                                <dt><?php esc_html_e( 'Cost', 'c3-table' ); ?></dt>
                                <dd><?php echo esc_html( $company_fields['advertised_price'] ? $company_fields['advertised_price'] : __( 'Unknown', 'c3-table' ) ); ?></dd>
                                <?php if ( in_array( 'location_tag', $additional_detail_fields ) ) : ?>
                                    <dt><?php esc_html_e( 'Location', 'c3-table' ); ?></dt>
                                    <dd><?php echo esc_html( $company_fields['location_tag'] ? $company_fields['location_tag'] : __( 'Unknown', 'c3-table' ) ); ?></dd>
                                <?php endif; ?>
                                <?php if ( in_array( 'start_year', $additional_detail_fields ) ) : ?>
                                    <dt><?php esc_html_e( 'Founded', 'c3-table' ); ?></dt>
                                    <dd><?php echo esc_html( $company_fields['start_year'] ? $company_fields['start_year'] : __( 'Unknown', 'c3-table' ) ); ?></dd>
                                <?php endif; ?>
                                <?php if ( in_array( 'team_size', $additional_detail_fields ) ) : ?>
                                    <dt><?php esc_html_e( 'Team Size', 'c3-table' ); ?></dt>
                                    <dd><?php echo esc_html( $company_fields['team_size'] ? $company_fields['team_size'] : __( 'Unknown', 'c3-table' ) ); ?></dd>
                                <?php endif; ?>
                                <?php if ( in_array( 'license', $additional_detail_fields ) ) : ?>
                                    <dt><?php esc_html_e( 'License', 'c3-table' ); ?></dt>
                                    <dd><?php echo esc_html( $company_fields['license'] ? $company_fields['license'] : __( 'Unknown', 'c3-table' ) ); ?></dd>
                                <?php endif; ?>
                            </dl>
                        </td>
                        <td class="cta">
                            <?php if ( ! empty( $company_fields['request_intro'] ) ) : ?>
                                <a href="<?php echo $company_link ? esc_url( $company_link ) : 'javascript:;'; ?>" class="cta-button"><?php echo esc_html( sprintf( __( 'Contact %s', 'c3-table' ), $company_name ) ); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * This function will print the CTA dialog to the page.
 *
 * It will generally be loaded onto the `wp_footer` action by the
 * `render_c3_table_block()` function and displayed via `c3-table.js`
 */
function render_dialog() {
	?>
<dialog id="contactDialog" closedby="any">
	<h3><?php esc_html_e( 'Feature Out of Scope!', 'c3-table' ); ?></h3>

    <p><?php esc_html_e( 'Wow, I\'m delighted that you\'ve explored my submission this deeply.  Unfortunately, actually building the contact was out of scope for this, but if built subsequently, the form can be dropped in here.', 'c3-table' ); ?></p>

    <p><?php esc_html_e( '- George Stephanis', 'c3-table' ); ?></p>

	<button onclick="window.contactDialog.close();"><?php _e( 'close', 'search-dialog' ); ?></button>
</dialog>
	<?php
}

/**
 * Manage custom columns for values post type
 *
 * @param array $columns The existing columns.
 * @return array The modified columns.
 */
function manage_columns( $columns ) {
	$columns = array_merge(
		array_slice( $columns, 0, 2 ),
        array( 'logo_url' => __( 'Logo', 'c3-table' ) ),
		array( 'location_tag' => __( 'Location', 'c3-table' ) ),
        array( 'rating' => __( 'Rating', 'c3-table' ) ),
		array( 'advertised_price' => __( 'Price', 'c3-table' ) ),
		array( 'team_size' => __( 'Team Size', 'c3-table' ) ),
		array( 'license' => __( 'License', 'c3-table' ) ),
		array( 'start_year' => __( 'Start Year', 'c3-table' ) ),
        array( 'claimed_profile' => __( 'Profile', 'c3-table' ) ),
        array( 'request_intro' => __( 'Intro', 'c3-table' ) ),
		array_slice( $columns, 2 )
	);
	return $columns;
}
add_filter( 'manage_company_posts_columns', __NAMESPACE__ . '\manage_columns' );

/**
 * Render custom column content for values post type
 *
 * @param string $column  The column name.
 * @param int    $post_id The post ID.
 * @return void
 */
function custom_column( $column, $post_id ) {
	switch ( $column ) {
		case 'location_tag':
        case 'advertised_price':
        case 'team_size':
        case 'license':
        case 'start_year':
			echo esc_html( get_field( $column, $post_id ) );
			break;
		case 'logo_url':
			$logo_url = get_field( 'logo_url', $post_id );
            if ( $logo_url && wp_http_validate_url( $logo_url ) ) {
                printf(
                    '<img src="%1$s" alt="%2$s" style="max-width:100px;height:auto;" />',
                    esc_url( $logo_url ),
                    esc_attr( get_the_title( $post_id ) )
                );
            } else {
                esc_html_e( 'None', 'c3-table' );
            }
			break;
        case 'rating':
            printf(
                esc_html__( 'Rating: %s', 'c3-table' ) . '<br />' . esc_html__( 'Reviews: %s', 'c3-table' ),
                '<strong>' . esc_html( get_field( 'avg_rating', $post_id ) ) . '</strong>/5',
                '<strong>' . esc_html( get_field( 'total_reviews', $post_id ) ) . '</string>'
            );
            break;
        case 'claimed_profile':
        case 'request_intro':
			echo get_field( $column, $post_id ) ? '✅' : '❌';
			break;
	}
}
add_action( 'manage_company_posts_custom_column', __NAMESPACE__ . '\custom_column', 10, 2 );

/**
 * Set some of the columns we're defining to hidden by default.
 *
 * @param array $hidden An array of column ids to default to hidden.
 * @param WP_Screen $screen A WP_Screen object for the current page.
 * @return array
 */
function default_hidden_columns( $hidden, $screen ) {
    if ( isset( $screen->id ) && 'edit-company' === $screen->id ) {
        $hidden[] = 'license';
        $hidden[] = 'advertised_price';
        $hidden[] = 'team_size';
        $hidden[] = 'start_year';
    }

    return $hidden;
}
add_filter( 'default_hidden_columns', __NAMESPACE__ . '\default_hidden_columns', 10, 2 );
