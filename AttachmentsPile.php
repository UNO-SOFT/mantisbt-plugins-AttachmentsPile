<?php
# :vim set noet:

define(MANTIS_DIR, dirname(__FILE__) . '/../..' );
define(MANTIS_CORE, MANTIS_DIR . '/core' );

require_once(MANTIS_DIR . '/core.php');
require_once( config_get( 'class_path' ) . 'MantisPlugin.class.php' );

class AttachmentsPilePlugin extends MantisPlugin {
	function register() {
		$this->name = 'AttachmentsPile';	# Proper name of plugin
		$this->description = 'Show the attachments as in 1.3';	# Short description of the plugin

		$this->version = '0.1';	 # Plugin version string
		$this->requires = array(	# Plugin dependencies, array of basename => version pairs
			'MantisCore' => '2.0.0',  #   Should always depend on an appropriate version of MantisBT
			);

		$this->author = 'Tamás Gulácsi';		 # Author/team name
		$this->contact = 'T.Gulacsi@unosoft.hu';		# Author/team e-mail address
		$this->url = 'http://www.unosoft.hu';			# Support webpage
	}

	function config() {
		return array();
	}

	function hooks() {
		return array(
			#'EVENT_VIEW_BUGNOTES_START' => 'view_bug_attachments',
			'EVENT_VIEW_BUG_DETAILS' => 'view_bug_attachments',
		);
	}

	function view_bug_attachments($p_event, $p_bug_id) {
		# log_event( LOG_EMAIL_RECIPIENT, "event=$p_event params=".var_export($p_bug_id, true) );
		require_once( MANTIS_CORE . '/bug_api.php' );
		require_once( MANTIS_CORE . '/config_api.php' );
		require_once( MANTIS_CORE . '/lang_api.php' );
		require_once( MANTIS_CORE . '/print_api.php' );
		require_once( MANTIS_CORE . '/user_api.php' );

		$t_attachments = file_get_visible_attachments( $p_bug_id );
		if( count( $t_attachments ) == 0) {
			return '';
		}
		echo '<tr class="spacer"><th class="bug-custom-field category">' ,
			string_display( lang_get_defaulted( 'attachments' ) ),
			'</th><td colspan="5"><div style="-webkit-column-count: 3; -moz-column-count: 3; column-count: 3;"><ul>';
		$t_security_token = form_security_token( 'bug_file_delete' );
		$t_date_format = config_get( 'normal_date_format' );
		foreach( $t_attachments as $t_attachment ) {
			$t_added = date( $t_date_format, $t_attachment['date_added'] );
			echo '<li><a href="#c' . $t_attachment['id'] . '">',
				$t_attachment['display_name'],
				'</a> ',
				'<span class="no-margin small lighter">(' . $t_added,
				', @' . user_get_name( $t_attachment['user_id'] ),
				')</span></li>' . "\n";
			//print_bug_attachment( $t_attachment, $t_security_token );
		}
		echo '</ul></div></td></tr>'."\n";
	}

}

# vim: set noet:
