/* Donate Plugin Scripts */
( function( $ ) {
	$( document ).ready( function() {
		$( '.dntplgn_monthly_other_sum' ).hide();
		$( '.dntplgn_donate_monthly input[ name="a3" ]' ).click( function() {
			if ( $( this ).parent( '.dntplgn_donate_monthly' ).children( '#fourth_button' ).attr( 'checked' ) ) {
				$( this ).parent( '.dntplgn_donate_monthly' ).children( '.dntplgn_monthly_other_sum' ).addClass( 'checked' );
				$( this ).parent( '.dntplgn_donate_monthly' ).children( '.dntplgn_submit_button' ).click( function() {
					$( this ).parent( '.dntplgn_donate_monthly' ).children( 'input[ name="a3" ]' ).val( $( this ).parent( '.dntplgn_donate_monthly' ).children( '.dntplgn_monthly_other_sum' ).val() );
				})
			} else {
				$( this ).parent( '.dntplgn_donate_monthly' ).children( '.dntplgn_monthly_other_sum' ).removeClass( 'checked' );
				$( this ).parent( '.dntplgn_donate_monthly' ).children( '.dntplgn_monthly_other_sum' ).val( '' );
			}
		});
		$( '.dntplgn_form_wrapper' ).tabs();
	});
})(jQuery)
