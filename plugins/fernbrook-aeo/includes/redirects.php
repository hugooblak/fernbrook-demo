<?php
/**
 * Redirects.
 *
 * When a site is restructured, the old addresses keep being linked to and keep being asked for.
 * A 404 throws away whatever authority that old page had; a 301 hands it to the new one.
 *
 * This screen also checks for the two mistakes that undo the whole point: chains (A → B → C)
 * and loops (A → B → A).
 *
 * @package Fernbrook AI Search
 */

defined( 'ABSPATH' ) || exit;

const FBAEO_REDIRECTS_OPTION = 'fbaeo_redirects';

/**
 * The redirect table.
 *
 * @return array[] Each: from, to, code, hits.
 */
function fbaeo_redirects() {
	$rows = get_option( FBAEO_REDIRECTS_OPTION, array() );
	return is_array( $rows ) ? $rows : array();
}

/**
 * Save the table.
 *
 * @param array $rows Rows.
 */
function fbaeo_save_redirects( $rows ) {
	update_option( FBAEO_REDIRECTS_OPTION, array_values( $rows ) );
}

/**
 * Tidy a path so "/Old-Page" and "old-page/" are the same thing.
 *
 * @param string $path Path or full address.
 * @return string
 */
function fbaeo_normalise_path( $path ) {
	$path = trim( (string) $path );
	if ( ! $path ) {
		return '';
	}
	if ( preg_match( '#^https?://#i', $path ) ) {
		$path = (string) wp_parse_url( $path, PHP_URL_PATH );
	}
	$path = strtolower( $path );
	$path = '/' . trim( $path, '/' );
	return '/' === $path ? '/' : $path . '/';
}

add_action(
	'template_redirect',
	function () {
		if ( is_admin() ) {
			return;
		}
		$request = fbaeo_normalise_path( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( ! $request ) {
			return;
		}
		$rows = fbaeo_redirects();
		foreach ( $rows as $i => $row ) {
			if ( fbaeo_normalise_path( $row['from'] ) !== $request ) {
				continue;
			}
			$target = $row['to'];
			if ( ! preg_match( '#^https?://#i', $target ) ) {
				$target = home_url( fbaeo_normalise_path( $target ) );
			}
			$rows[ $i ]['hits'] = (int) ( $row['hits'] ?? 0 ) + 1;
			fbaeo_save_redirects( $rows );
			wp_safe_redirect( $target, (int) ( $row['code'] ?? 301 ) );
			exit;
		}
	},
	1
);

/**
 * Problems in the table: chains, loops, and rules that point at a page that no longer exists.
 *
 * @return array[] Each: from, problem.
 */
function fbaeo_redirect_problems() {
	$rows     = fbaeo_redirects();
	$by_from  = array();
	$problems = array();
	foreach ( $rows as $row ) {
		$by_from[ fbaeo_normalise_path( $row['from'] ) ] = fbaeo_normalise_path( $row['to'] );
	}
	foreach ( $by_from as $from => $to ) {
		if ( $from === $to ) {
			$problems[] = array( $from, __( 'Points at itself. Delete this rule.', 'fernbrook-aeo' ) );
			continue;
		}
		$seen = array( $from );
		$next = $to;
		$hops = 0;
		while ( isset( $by_from[ $next ] ) && $hops < 10 ) {
			if ( in_array( $next, $seen, true ) ) {
				$problems[] = array( $from, __( 'Loop: this rule and another one send visitors back and forth.', 'fernbrook-aeo' ) );
				break 1;
			}
			$seen[] = $next;
			$next   = $by_from[ $next ];
			$hops++;
			if ( 1 === $hops ) {
				$problems[] = array( $from, sprintf( /* translators: %s: final address */ __( 'Chain: this goes through another redirect. Point it straight at %s.', 'fernbrook-aeo' ), $next ) );
			}
		}
	}
	return $problems;
}

/**
 * The Redirects screen.
 */
function fbaeo_screen_redirects() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to manage redirects.', 'fernbrook-aeo' ) );
	}
	$notice = '';

	if ( isset( $_POST['fbaeo_redirects_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fbaeo_redirects_nonce'] ) ), 'fbaeo_redirects' ) ) {
		$rows = array();
		$from = isset( $_POST['from'] ) ? (array) wp_unslash( $_POST['from'] ) : array();
		$to   = isset( $_POST['to'] ) ? (array) wp_unslash( $_POST['to'] ) : array();
		$code = isset( $_POST['code'] ) ? (array) wp_unslash( $_POST['code'] ) : array();
		$hits = isset( $_POST['hits'] ) ? (array) wp_unslash( $_POST['hits'] ) : array();
		foreach ( $from as $i => $value ) {
			$value = sanitize_text_field( $value );
			$dest  = sanitize_text_field( $to[ $i ] ?? '' );
			if ( ! $value || ! $dest ) {
				continue;
			}
			$rows[] = array(
				'from' => fbaeo_normalise_path( $value ),
				'to'   => $dest,
				'code' => in_array( (int) ( $code[ $i ] ?? 301 ), array( 301, 302 ), true ) ? (int) $code[ $i ] : 301,
				'hits' => absint( $hits[ $i ] ?? 0 ),
			);
		}
		$csv = isset( $_POST['csv'] ) ? sanitize_textarea_field( wp_unslash( $_POST['csv'] ) ) : '';
		if ( trim( $csv ) ) {
			foreach ( preg_split( '/\R/', trim( $csv ) ) as $line ) {
				$parts = str_getcsv( $line );
				if ( count( $parts ) < 2 || 'from' === strtolower( trim( $parts[0] ) ) ) {
					continue;
				}
				$rows[] = array(
					'from' => fbaeo_normalise_path( $parts[0] ),
					'to'   => trim( $parts[1] ),
					'code' => isset( $parts[2] ) && '302' === trim( $parts[2] ) ? 302 : 301,
					'hits' => 0,
				);
			}
		}
		fbaeo_save_redirects( $rows );
		$notice = __( 'Redirects saved.', 'fernbrook-aeo' );
	}

	$rows     = fbaeo_redirects();
	$problems = fbaeo_redirect_problems();
	?>
	<div class="wrap fbaeo-wrap">
		<h1><?php esc_html_e( 'Redirects', 'fernbrook-aeo' ); ?></h1>
		<?php if ( $notice ) : ?>
			<div class="notice notice-success"><p><?php echo esc_html( $notice ); ?></p></div>
		<?php endif; ?>
		<p class="fbaeo-intro"><?php esc_html_e( 'One line per old address. A 301 tells search engines the move is permanent and passes on whatever the old page had earned. Use 302 only while you are testing.', 'fernbrook-aeo' ); ?></p>

		<?php if ( $problems ) : ?>
			<div class="notice notice-warning"><p><strong><?php esc_html_e( 'Problems found:', 'fernbrook-aeo' ); ?></strong></p><ul style="list-style:disc;margin-left:1.4rem">
			<?php foreach ( $problems as $problem ) : ?>
				<li><code><?php echo esc_html( $problem[0] ); ?></code> — <?php echo esc_html( $problem[1] ); ?></li>
			<?php endforeach; ?>
			</ul></div>
		<?php else : ?>
			<div class="notice notice-success inline"><p><?php esc_html_e( 'No chains and no loops. Every rule goes straight to its destination.', 'fernbrook-aeo' ); ?></p></div>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'fbaeo_redirects', 'fbaeo_redirects_nonce' ); ?>
			<table class="widefat striped fbaeo-table">
				<thead><tr>
					<th><?php esc_html_e( 'Old address', 'fernbrook-aeo' ); ?></th>
					<th><?php esc_html_e( 'New address', 'fernbrook-aeo' ); ?></th>
					<th style="width:6rem"><?php esc_html_e( 'Code', 'fernbrook-aeo' ); ?></th>
					<th style="width:6rem"><?php esc_html_e( 'Times used', 'fernbrook-aeo' ); ?></th>
				</tr></thead>
				<tbody>
				<?php foreach ( array_merge( $rows, array( array( 'from' => '', 'to' => '', 'code' => 301, 'hits' => 0 ), array( 'from' => '', 'to' => '', 'code' => 301, 'hits' => 0 ) ) ) as $row ) : ?>
					<tr>
						<td><input type="text" name="from[]" class="large-text" value="<?php echo esc_attr( $row['from'] ); ?>" placeholder="/old-page/"></td>
						<td><input type="text" name="to[]" class="large-text" value="<?php echo esc_attr( $row['to'] ); ?>" placeholder="/new-page/"></td>
						<td>
							<select name="code[]">
								<option value="301" <?php selected( (int) $row['code'], 301 ); ?>>301</option>
								<option value="302" <?php selected( (int) $row['code'], 302 ); ?>>302</option>
							</select>
						</td>
						<td><?php echo (int) $row['hits']; ?><input type="hidden" name="hits[]" value="<?php echo (int) $row['hits']; ?>"></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<h2><?php esc_html_e( 'Import from a CSV', 'fernbrook-aeo' ); ?></h2>
			<p class="description"><?php esc_html_e( 'One rule per line: old address, new address, code. Paste the list your old site gave you and save. A header row is ignored.', 'fernbrook-aeo' ); ?></p>
			<textarea name="csv" rows="4" class="large-text" placeholder="/puppy-class/,/puppy-foundations/,301"></textarea>

			<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Save redirects', 'fernbrook-aeo' ); ?></button></p>
		</form>

		<h2><?php esc_html_e( 'Export', 'fernbrook-aeo' ); ?></h2>
		<textarea rows="6" class="large-text" readonly><?php
		echo esc_textarea( "from,to,code\n" );
		foreach ( $rows as $row ) {
			echo esc_textarea( $row['from'] . ',' . $row['to'] . ',' . (int) $row['code'] . "\n" );
		}
		?></textarea>
	</div>
	<?php
}
