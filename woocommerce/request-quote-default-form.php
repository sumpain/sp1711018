<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Form to Request a quote
 *
 * @package Yithemes
 * @since   2.0.0
 * @author  Yithemes
 */

?>
<div class="yith-ywraq-mail-form-wrapper">
    <h3><?php echo apply_filters( 'ywraq_form_title', __( 'Send the request', 'yith-woocommerce-request-a-quote' ) ) ?></h3>
    <form id="yith-ywraq-default-form" name="yith-ywraq-default-form"
          action="<?php echo esc_url( YITH_Request_Quote()->get_raq_page_url() ) ?>" method="post"
          enctype="multipart/form-data">
        <input type="hidden" id="yith-ywraq-default-form-sent" name="yith-ywraq-default-form-sent" value="1">

		<h5>Contact Details</h5>
		<div class="form-fieldset">
	    <?php do_action( 'ywraq_before_content_default_form' ); ?>
	    <?php
            foreach ( $fields as $key => $field ) {
                if ( isset( $field['enabled'] ) && $field['enabled'] ) {
					if ($key=="address_1") { ?>
			<div style="clear:both"></div>
		</div>
		<h5>Billing Details</h5>
		<div class="form-fieldset">
		<?php }
					if ($key=="delivery_options") { ?>
			<div style="clear:both"></div>
		</div>
		<h5>Delivery &amp; Collection</h5>
		<div class="form-fieldset">
		<?php }
					if ($key=="collection_date") { ?>
			<div id="delivery_option_collection">
				<p>You will collect it in person and return it to us.</p>
		<?php }
					if ($key=="delivery_date") { ?>
			</div>
			<div id="delivery_option_delivery">				
				<p>Bentley Brown deliver and collect it for you.</p>
		<?php }
					if ($key=="shipping_first_name") { ?>
			</div>
			<div id="shipping_address">
		<?php }
					if ($key=="delivery_instructions") { ?>
			</div>
			<div>
		<?php }
                    woocommerce_form_field( $key, $field, YITH_YWRAQ_Default_Form()->get_value( $key, $field ) );
                }
            }
		?>
			</div>
			<div style="clear:both"></div>
		</div>
	    <?php if ( ! is_user_logged_in() && 'yes' == $registration_is_enabled ) : ?>
		    <?php do_action( 'ywraq_before_registration_default_form' ); ?>
            <div class="woocommerce-account-fields">
				<h5>Login Details</h5>
				<div class="form-fieldset">
	            <?php if ( 'no' == $force_registration ) : ?>
                    <p class="form-row form-row-wide create-account">
                        <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                            <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
                                   id="createaccount" <?php checked( true === apply_filters( 'ywraq_create_account_default_checked', false ), true ) ?>
                                   type="checkbox" name="createaccount" value="1"/>
                            <span><?php _e( 'Create an account?', 'yith-woocommerce-request-a-quote' ); ?></span>
                        </label>
                    </p>
	            <?php endif; ?>

			    <?php if ( $account_fields ) : ?>
                    <div class="create-account">
					    <?php foreach ( $account_fields as $key => $field ) : ?>
						    <?php woocommerce_form_field( $key, $field, '' ); ?>
					    <?php endforeach; ?>
                        <div class="clear"></div>
                    </div>
			    <?php endif; ?>
				</div>
            </div>
		    <?php do_action( 'ywraq_after_registration_default_form' ); ?>
	    <?php endif; ?>

		<?php if ( ywraq_check_recaptcha_options() ) : ?>
            <p class="form-row form-row form-row-wide">
            <div class="g-recaptcha"
                 data-sitekey="<?php echo esc_attr( get_option( 'ywraq_reCAPTCHA_sitekey' ) ) ?>"></div>
            </p>
		<?php endif; ?>

        <p class="form-row form-row-wide">
            <input type="hidden" id="ywraq-mail-wpnonce" name="ywraq_mail_wpnonce"
                   value="<?php echo wp_create_nonce( 'ywraq-default-form-request' ) ?>">
            <input class="button raq-send-request" type="submit"
                   value="<?php echo apply_filters( 'ywraq_form_defaul_submit_label', __( 'Send Your Request', 'yith-woocommerce-request-a-quote' ) ) ?>">
        </p>

		<?php if ( defined( 'ICL_LANGUAGE_CODE' ) ): ?>
            <input type="hidden" class="lang_param" name="lang" value="<?php echo( ICL_LANGUAGE_CODE ); ?>"/>
		<?php endif ?>

	    <?php do_action( 'ywraq_after_content_default_form' ); ?>
    </form>
</div>

<script>
var delopthtml = "";
var shipaddrhtml = "";
jQuery(document).ready(function(){
	jQuery('.raq-send-request').attr('type','button');
	jQuery('.raq-send-request').on('click', function(){ submitForm(); });
	jQuery('input[name=delivery_options]').eq(0).attr('checked','checked');
	jQuery('input[name=delivery_address]').eq(0).attr('checked','checked');
	jQuery('.optional').html("");
	jQuery('#delivery_option_collection label').append('<abbr class="required" title="required">*</abbr>');
	jQuery('#delivery_option_delivery label').append('<abbr class="required" title="required">*</abbr>');
	jQuery('#shipping_address label').append('<abbr class="required" title="required">*</abbr>');
	jQuery('#shipping_company_field label abbr').remove();
	jQuery('input[name=delivery_options]').change(function(){
		if (jQuery(this).val()=='delivery-collection'){
			jQuery('#delivery_option_collection').hide();
			jQuery('#delivery_option_delivery').show();
		} else {
			jQuery('#delivery_option_delivery').hide();
			jQuery('#delivery_option_collection').show();
		}
	});
	jQuery('input[name=delivery_address]').change(function(){
		jQuery('#shipping_address').toggle();
	});
	jQuery('#delivery_date').click(function(){
		jQuery('#ui-datepicker-div').css({left:jQuery(this).offset().left,top:jQuery(this).offset().top+jQuery(this).height()});
	});
	jQuery('#collection_date2').click(function(){
		jQuery('#ui-datepicker-div').css({left:jQuery(this).offset().left,top:jQuery(this).offset().top+jQuery(this).height()});
	});
});

function submitForm(){
	var deltype = jQuery('input[name=delivery_options]:checked').val();
	var date1;
	var date2;
	if (deltype=="customer-collect-return") {
		date1 = jQuery('#collection_date');
		date2 = jQuery('#return_date');
	} else {
		date1 = jQuery('#delivery_date');
		date2 = jQuery('#collection_date2');
	}
	jQuery(date1).parent().find('.ywraq_error').remove();
	jQuery(date2).parent().find('.ywraq_error').remove();
	if (jQuery(date1).val()=="") { jQuery(date1).parent().append('<span class="ywraq_error">This is a required field.</span>'); }
	if (jQuery(date2).val()=="") { jQuery(date2).parent().append('<span class="ywraq_error">This is a required field.</span>'); }
	var shipaddress = jQuery("#delivery_address").is(":checked");
	if (!shipaddress) {
		jQuery("#shipping_address .ywraq_error").remove();
		if (jQuery("#shipping_first_name").val()=="" || jQuery("#shipping_last_name").val()=="" || jQuery("#shipping_address_1").val()=="" || jQuery("#shipping_city").val()=="" || jQuery("#shipping_county").val()=="" || jQuery("#shipping_postcode").val()=="" || jQuery("#shipping_country").val()=="") {
			jQuery("#shipping_address").prepend('<span class="ywraq_error" style="display:block">Please fill in all required fields.</span>');
		} else {
			jQuery("#yith-ywraq-default-form").submit();
		}
	} else {
		jQuery("#yith-ywraq-default-form").submit();
	}
}
</script>