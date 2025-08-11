<?php

class Security
{
	public static function password_verify( #[\SensitiveParameter] string $password, string $hash ): bool
	{
		return password_verify( $password, $hash );
	}

	public static function password_hash( #[\SensitiveParameter] string $password )
	{
		return password_hash( $password, PASSWORD_DEFAULT );
	}
}
