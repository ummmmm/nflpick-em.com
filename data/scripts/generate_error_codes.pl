#!/usr/bin/perl

use strict;
use warnings;
use File::Basename;

process_file( $_ ) foreach ( @ARGV );

sub process_file
{
	my $input_file 				= shift;
	my $output_file 			= $input_file . ".tmp";
	my $error_code_count			= 0;
	my ( $filename, $directories, $suffix ) = fileparse( $input_file, qr/\.[^.]*/ );
	my $error_code_prefix 			= 'NFL-' . uc( $filename ) . '-';
	
	open( my $input, "<:encoding(utf8)", $input_file ) or die "Unable to open $input_file: $!";
	open( my $output, ">:encoding(utf8)", $output_file ) or die "Unable to open $output_file: $!";

	while( my $line = <$input> )
	{
		$error_code_count++ if $line =~ s/'$error_code_prefix[0-9]+'/'$error_code_prefix$error_code_count'/;
		$error_code_count++ if $line =~ s/'#Error#'/'$error_code_prefix$error_code_count'/;
		
		print $output $line;
	}

	close $output;
	close $input;

	print "Failed to remove $input_file: $!" unless unlink $input_file;
	print "Failed to rename $output_file: $!" unless rename $output_file, $input_file;
	print "Set $error_code_count error codes in $input_file\n" if defined $error_code_prefix;
}
