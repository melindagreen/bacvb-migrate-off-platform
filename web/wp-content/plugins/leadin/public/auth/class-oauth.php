<?php

namespace Leadin\auth;

use Leadin\data\User;
use Leadin\data\Portal_Options;
use Leadin\client\Access_Token_Api_Client;
use Leadin\auth\OAuthCrypto;
use Leadin\admin\Routing;
use Leadin\admin\MenuConstants;

/**
 * Class managing OAuth2 authorization
 */
class OAuth {

	/**
	 * Return the flag checking if we're connected with OAuth.
	 *
	 * @return bool True if the OAuth version of the plugin is enabled or not.
	 */
	public static function is_enabled() {
		return ! empty( Portal_Options::get_refresh_token() );
	}

	/**
	 * Authorizes the plugin with given oauth credentials by storing them in the options DB.
	 *
	 * @param string $refresh_token OAuth refresh token to store.
	 */
	public static function authorize( $refresh_token ) {
		$encrypted_refresh_token = OAuthCrypto::encrypt( $refresh_token );
		Portal_Options::set_refresh_token( $encrypted_refresh_token );
	}

	/**
	 * Deauthorizes the plugin by deleting OAuth credentials from the options DB.
	 */
	public static function deauthorize() {
		Portal_Options::delete_access_token();
		Portal_Options::delete_refresh_token();
		Portal_Options::delete_expiry_time();
	}

	/**
	 * Returns the refresh token stored in the options table.
	 *
	 * @return string The stored refresh token in the Options table.
	 *
	 * @throws \Exception If no refresh token is available.
	 */
	public static function get_refresh_token() {
		$encrypted_refresh_token = Portal_Options::get_refresh_token();

		if ( '' === $encrypted_refresh_token ) {
			self::deauthorize();
			throw new \Exception( 'Refresh token is empty' );
		}

		return OAuthCrypto::decrypt( $encrypted_refresh_token );
	}

}
