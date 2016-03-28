<?php
    # Not a valid entry point, skip unless MEDIAWIKI is defined
    if (!defined('MEDIAWIKI')) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/cedarcontacts/cedarcontacts.php" );
EOT;
	exit( 1 );
    }
    
    $dir = dirname( __FILE__ ) . '/' ;
    $wgAutoloadClasses['CedarContacts'] = $dir .  'CedarContacts_body.php';
    $wgExtensionMessagesFiles['CedarContacts'] = $dir .  'CedarContacts.i18n.php';
    $wgSpecialPages['CedarContacts'] = 'CedarContacts';
    $wgHooks['LanguageGetSpecialPageAliases'][] = 'CedarContactsLocalizedPageName';

    function CedarContactsLocalizedPageName( &$specialPageArray, $code )
    {
	# The localized title of the special page is among the messages of
	# the extension:
	#wfLoadExtensionMessages('CedarContacts');
	$text = wfMsg('cedarcontacts');

	# Convert from title in text form to DBKey and put it into the
	# alias array:
	$title = Title::newFromText($text);
	$specialPageArray['CedarContacts'][] = $title->getDBKey();

	return true;
    }

    $wgExtensionCredits['other'][]=array(
	'name' => 'Cedar Contacts',
	'version' => '1.0',
	'author' => 'Patrick West',
	'url' => 'http://cedarweb.hao.ucar.edu',
	'description' => 'Be able to manage CEDAR contacts'
    );
?>
