<?php
class CedarContacts extends SpecialPage
{
    var $dbuser, $dbpwd ;

    function CedarContacts() {
	SpecialPage::SpecialPage("CedarContacts");
	#wfLoadExtensionMessages( 'CedarContacts' ) ;

	$this->dbuser = "madrigal" ;
	$this->dbpwd = "shrot-kash-iv-po" ;
    }
    
    function execute( $par ) {
	global $wgRequest, $wgOut, $wgDBserver, $wgServer ;
	
	$this->setHeaders();

	$action = $wgRequest->getText('action');
	if( $action == "addRole" )
	{
	    $this->editRole() ;
	}
	else if( $action == "editRole" )
	{
	    $this->editRole() ;
	}
	else if( $action == "submitRole" )
	{
	    $this->submitRole() ;
	}
	else if( $action == "delRole" )
	{
	    $this->delRole() ;
	}
	else if( $action == "addOrg" )
	{
	    $this->editOrg() ;
	}
	else if( $action == "editOrg" )
	{
	    $this->editOrg() ;
	}
	else if( $action == "submitOrg" )
	{
	    $this->submitOrg() ;
	}
	else if( $action == "delOrg" )
	{
	    $this->delOrg() ;
	}
	else if( $action == "addPerson" )
	{
	    $this->editPerson() ;
	}
	else if( $action == "editPerson" )
	{
	    $this->editPerson() ;
	}
	else if( $action == "addPRole" )
	{
	    $this->addPRole() ;
	}
	else if( $action == "submitPRole" )
	{
	    $this->submitPRole() ;
	}
	else if( $action == "delPRole" )
	{
	    $this->delPRole() ;
	}
	else if( $action == "submitPerson" )
	{
	    $this->submitPerson() ;
	}
	else if( $action == "delPerson" )
	{
	    $this->delPerson() ;
	}
	else
	{
	    $this->displayList() ;
	}
    }

    private function displayList()
    {
	global $wgDBserver, $wgOut, $wgServer, $wgUser ;

	$allowed = $wgUser->isAllowed( 'cedar_admin' ) ;

	// Connect to the CEDARCATALOG database
	$dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	if( !$dbh || !$dbh->isOpen() )
	{
	    $wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
	    if( $dbh )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "$db_error<br /> \n" ) ;
	    }
	    return ;
	}

	$wgOut->addHTML( "<h2>Person List</h2>\n" ) ;
	if( $allowed )
	{
	    $wgOut->addHTML( "<ul>\n" ) ;
	    $wgOut->addHTML( "<li>\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=addPerson\">Add Person</a>\n" ) ;
	    $wgOut->addHTML( "</li>\n" ) ;
	    $wgOut->addHTML( "</ul>\n" ) ;
	}
	#$sql = "SELECT person_id, user_id, user_phone, user_fax, user_url FROM tbl_person;" ;
	$sql = "SELECT p.person_id, p.user_id, u.user_name, u.user_email, u.user_real_name, p.user_phone, p.user_fax, p.user_url FROM tbl_person p,wikidb.user u WHERE p.user_id = u.user_id GROUP BY RIGHT( u.user_real_name, LOCATE( ' ', REVERSE( u.user_real_name) ) -1 );" ;
	$res = $dbh->query( $sql ) ;
	if( !$res )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<BR />\n" ) ;
	    return ;
	}
	$wgOut->addHTML( "<table border=\"1\" cellspacing=\"2\" cellpadding=\"2\">\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	if( $allowed )
	{
	    $wgOut->addHTML( "<td>\n" ) ;
	    $wgOut->addHTML( "&nbsp;\n" ) ;
	    $wgOut->addHTML( "</td>\n" ) ;
	}
	$wgOut->addHTML( "<td>\n" ) ;
	$wgOut->addHTML( "Name\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td>\n" ) ;
	$wgOut->addHTML( "Roles\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	while( ( $obj = $dbh->fetchObject( $res ) ) )
	{
	    $pid = $obj->person_id ;
	    $uid = $obj->user_id ;
	    $phone = $obj->user_phone ;
	    $fax = $obj->user_fax ;
	    $url = $obj->user_url ;
	    $rname = $obj->user_real_name ;
	    $uname = $obj->user_name ;
	    $email = $obj->user_email ;

	    if( !$rname || $rname == "" )
	    {
		$rname = $uname ;
	    }
	    $wgOut->addHTML( "<tr>\n" ) ;
	    if( $allowed )
	    {
		$wgOut->addHTML( "<td>\n" ) ;
		$wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=editPerson&person_id=$pid\"><img src='$wgServer/wiki/icons/edit.png' alt='edit' title='Edit'></a>" ) ;
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=delPerson&person_id=$pid\"><img src='$wgServer/wiki/icons/delete.png' alt='delete' title='Delete'></a>" ) ;
		$wgOut->addHTML( "</td>\n" ) ;
	    }
	    $wgOut->addHTML( "<td valign=\"top\">\n" ) ;
	    if( $email != "" )
	    {
		$wgOut->addWikiText( "[mailto:$email\ $rname]\n" ) ;
	    }
	    else
	    {
		$wgOut->addHTML( "$rname\n" ) ;
	    }
	    if( $phone != "" )
	    {
		$wgOut->addWikiText( "* phone: $phone\n" ) ;
	    }
	    if( $fax != "" )
	    {
		$wgOut->addWikiText( "* fax: $fax\n" ) ;
	    }
	    if( $url != "" )
	    {
		$wgOut->addWikiText( "* [$url link]\n" ) ;
	    }
	    $wgOut->addHTML( "</td>\n" ) ;
	    $wgOut->addHTML( "<td valign=\"top\">\n" ) ;
	    $this->displayRoles( $allowed, $dbh, $pid ) ;
	    $wgOut->addHTML( "</td>\n" ) ;
	    $wgOut->addHTML( "</tr>\n" ) ;
	}
	$wgOut->addHTML( "</table>\n" ) ;

	if( $allowed )
	{
	    $wgOut->addHTML( "<h2>Role List</h2>\n" ) ;
	    $wgOut->addHTML( "<ul>\n" ) ;
	    $wgOut->addHTML( "<li>\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=addRole\">Add Role</a>\n" ) ;
	    $wgOut->addHTML( "</li>\n" ) ;
	    $wgOut->addHTML( "</ul>\n" ) ;
	    $sql = "SELECT role_id, role_name FROM tbl_role;" ;
	    $res = $dbh->query( $sql ) ;
	    if( !$res )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
		$wgOut->addHTML( $db_error ) ;
		$wgOut->addHTML( "<BR />\n" ) ;
		return ;
	    }
	    $wgOut->addHTML( "<table border=\"1\" cellspacing=\"2\" cellpadding=\"2\">\n" ) ;
	    while( ( $obj = $dbh->fetchObject( $res ) ) )
	    {
		$role_name = $obj->role_name ;
		$rid = $obj->role_id ;
		$wgOut->addHTML( "<tr>\n" ) ;
		$wgOut->addHTML( "<td>\n" ) ;
		$wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=editRole&role_id=$rid\"><img src='$wgServer/wiki/icons/edit.png' alt='edit' title='Edit'></a>" ) ;
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=delRole&role_id=$rid\"><img src='$wgServer/wiki/icons/delete.png' alt='delete' title='Delete'></a>" ) ;
		$wgOut->addHTML( "</td>\n" ) ;
		$wgOut->addHTML( "<td>\n" ) ;
		$wgOut->addHTML( "$role_name\n" ) ;
		$wgOut->addHTML( "</td>\n" ) ;
		$wgOut->addHTML( "</tr>\n" ) ;
	    }
	    $wgOut->addHTML( "</table>\n" ) ;

	    $wgOut->addHTML( "<h2>Organization List</h2>\n" ) ;
	    $wgOut->addHTML( "<ul>\n" ) ;
	    $wgOut->addHTML( "<li>\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=addOrg\">Add Organization</a>\n" ) ;
	    $wgOut->addHTML( "</li>\n" ) ;
	    $wgOut->addHTML( "</ul>\n" ) ;
	    $sql = "SELECT organization_id, organization_name FROM tbl_organization;" ;
	    $res = $dbh->query( $sql ) ;
	    if( !$res )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
		$wgOut->addHTML( $db_error ) ;
		$wgOut->addHTML( "<BR />\n" ) ;
		return ;
	    }
	    $wgOut->addHTML( "<table border=\"1\" cellspacing=\"2\" cellpadding=\"2\">\n" ) ;
	    while( ( $obj = $dbh->fetchObject( $res ) ) )
	    {
		$org_name = $obj->organization_name ;
		$oid = $obj->organization_id ;
		$wgOut->addHTML( "<tr>\n" ) ;
		$wgOut->addHTML( "<td>\n" ) ;
		$wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=editOrg&org_id=$oid\"><img src='$wgServer/wiki/icons/edit.png' alt='edit' title='Edit'></a>" ) ;
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=delOrg&org_id=$oid\"><img src='$wgServer/wiki/icons/delete.png' alt='delete' title='Delete'></a>" ) ;
		$wgOut->addHTML( "</td>\n" ) ;
		$wgOut->addHTML( "<td>\n" ) ;
		$wgOut->addHTML( "$org_name\n" ) ;
		$wgOut->addHTML( "</td>\n" ) ;
		$wgOut->addHTML( "</tr>\n" ) ;
	    }
	    $wgOut->addHTML( "</table>\n" ) ;
	}

    }

    private function displayRoles( $allowed, $dbh, $pid )
    {
	global $wgOut ;

	$sql = "SELECT r.role_context, r.context_column, p.context_id, r.role_name, p.person_role_id FROM tbl_role r,tbl_person_role p WHERE p.role_id = r.role_id AND p.person_id = $pid GROUP BY p.context_id" ;
	$res = $dbh->query( $sql ) ;
	if( !$res )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<BR />\n" ) ;
	    return ;
	}

	$wgOut->addHTML( "<ul>\n" ) ;
	if( $allowed )
	{
	    $wgOut->addHTML( "<li>\n" ) ;
	    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=addPRole&person_id=$pid\">Add Role</a>\n" ) ;
	    $wgOut->addHTML( "</li>\n" ) ;
	}

	while( ( $obj = $dbh->fetchObject( $res ) ) )
	{
	    $role_name = $obj->role_name ;
	    $prid = $obj->person_role_id ;
	    $context = $obj->role_context ;
	    $column = $obj->context_column ;
	    $cid = $obj->context_id ;
	    $str = "$role_name" . $this->displayContext( $dbh, $context, $column, $cid );
	    $wgOut->addHTML( "<li>\n" ) ;
	    if( $allowed )
	    {
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=delPRole&person_role_id=$prid\"><img src='$wgServer/wiki/icons/delete.png' alt='delete' title='Delete'></a>" ) ;
	    }
	    $wgOut->addHTML( "$str</li>\n" ) ;
	}
	$wgOut->addHTML( "</ul>\n" ) ;
    }

    private function displayContext( $dbh, $context, $column, $cid )
    {
	$ret_str = "" ;
	if( $context == "tbl_instrument" )
	{
	    $sql = "SELECT PREFIX, INST_NAME FROM tbl_instrument where $column = $cid" ;
	    $res = $dbh->query( $sql ) ;
	    if( $res )
	    {
		$obj = $dbh->fetchObject( $res ) ;
		if( $obj )
		{
		    $prefix = $obj->PREFIX ;
		    $name = $obj->INST_NAME ;
		    $ret_str = " for instrument $cid - $prefix - $name" ;
		}
	    }
	}
	else if( $context == "tbl_protected_text" )
	{
	    $ret_str = " for protexted files *$cid*" ;
	}
	return $ret_str ;
    }

    private function editRole()
    {
	global $wgRequest, $wgOut, $wgDBserver, $wgServer, $wgUser ;

	$allowed = $wgUser->isAllowed( 'cedar_admin' ) ;

	if( !$allowed )
	{
	    $wgOut->addHTML( "You are not allowed to create new CEDAR contacts<br />\n" ) ;
	    return ;
	}

	$wgOut->addHTML( "<h1>Add/Edit a Role</h1>\n" ) ;

	$dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	if( !$dbh || !$dbh->isOpen() )
	{
	    $wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
	    if( $dbh )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "$db_error<br /> \n" ) ;
	    }
	    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}

	$rid = $wgRequest->getText('role_id') ;
	$rname = $wgRequest->getText('role_name') ;
	$pid = $wgRequest->getText('parent_id') ;
	if( $rid && $rid != 0 )
	{
	    if( ( !$rname || $rname == "" ) && ( !$pid || $pid == 0 ) )
	    {
		// Go get the information for this role from db
		$sql = "SELECT role_name, parent_id FROM tbl_role WHERE role_id = $rid;" ;
		$res = $dbh->query( $sql ) ;
		if( !$res )
		{
		    $db_error = $dbh->lastError() ;
		    $wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
		    $wgOut->addHTML( $db_error ) ;
		    $wgOut->addHTML( "<BR />\n" ) ;
		    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		    return ;
		}
		// role_id is uniq so we should get at least one
		$obj = $dbh->fetchObject( $res ) ;
		if( $obj )
		{
		    $rname = $obj->role_name ;
		    $pid = $obj->parent_id ;
		}
	    }
	    // else they've edited the role but something is wrong
	}
	// Display the form
	$wgOut->addHTML( "<form name=\"cedarcontact\" action=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\" method=\"POST\">\n" ) ;
	$wgOut->addHTML( "<input type=\"hidden\" name=\"action\" value=\"submitRole\">\n" ) ;
	$wgOut->addHTML( "<input type=\"hidden\" name=\"role_id\" value=\"$rid\">\n" ) ;
	$wgOut->addHTML( "<table width=\"400px\" border=\"0\" cellpadding=\"2\" cellspacing=\"2\">\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "Role Name:&nbsp;&nbsp;\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"text\" name=\"role_name\" value=\"$rname\" SIZE=\"30\" />\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "Parent Role:&nbsp;&nbsp;\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<select name=\"parent_id\" size=\"1\">\n" ) ;
	if( !$pid || $pid == 0 ) $pselect = "SELECTED" ;
	$wgOut->addHTML( "  <option value=\"\" $pselect> </option>\n" ) ;
	if( $rid && $rid != 0 ) $where = "WHERE role_id != $rid" ;
	$sql = "SELECT role_id,role_name FROM tbl_role $where;" ;
	$res = $dbh->query( $sql ) ;
	if( !$res )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<BR />\n" ) ;
	    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	while( $obj = $dbh->fetchObject( $res ) )
	{
	    $prole_name = $obj->role_name ;
	    $prole_id = $obj->role_id ;
	    if( $pid && $pid == $prole_id ) $is_selected="SELECTED" ;
	    else $is_selected="" ;
	    $wgOut->addHTML( "  <option value=\"$prole_id\" $is_selected>$prole_name</option>\n" ) ;
	}
	$wgOut->addHTML( "</select>\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"submit\" name=\"submit\" value=\"Submit\">\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"reset\" value=\"Reset\">\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "</table>\n" ) ;
	$wgOut->addHTML( "</form>\n" ) ;
    }

    private function submitRole()
    {
	global $wgRequest, $wgOut, $wgDBserver, $wgServer, $wgUser ;

	$allowed = $wgUser->isAllowed( 'cedar_admin' ) ;

	if( !$allowed )
	{
	    $wgOut->addHTML( "You are not allowed to create new CEDAR contacts<br />\n" ) ;
	    return ;
	}
	$rid = $wgRequest->getText('role_id') ;
	$rname = $wgRequest->getText('role_name') ;
	$pid = $wgRequest->getText('parent_id') ;
	if( !$rname || $rname == "" )
	{
	    $wgOut->addHTML( "<span style=\"color:red;\">You must enter a role name<br /><br />\n" ) ;
	    $this->editRole() ;
	    return ;
	}

	$dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	if( !$dbh || !$dbh->isOpen() )
	{
	    $wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
	    if( $dbh )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "$db_error<br /> \n" ) ;
	    }
	    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	$role_table = $dbh->tableName( 'tbl_role' ) ;
	$rid = $dbh->strencode( $rid ) ;
	$rname = $dbh->strencode( $rname ) ;
	$pid = $dbh->strencode( $pid ) ;

	if( !$rid || $rid == 0 )
	{
	    // adding new role
	    $insert_success = $dbh->insert( $role_table,
		    array(
			'role_name' => "$rname",
			'parent_id' => "$pid",
		    ),
		    __METHOD__
		) ;
	}
	else
	{
	    // updating existing role
	    $insert_success = $dbh->update( $role_table,
		    array(
			'role_name' => "$rname",
			'parent_id' => "$pid",
		    ), array( /* WHERE */
			    'role_id' => $rid
		    ), __METHOD__
		) ;
	}
	if( $insert_success == false )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Failed to insert/update role $rname<br />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    $wgOut->addHTML( "Please contact <a href=\"mailto:cedar_db@hao.ucar.edu\">Cedar Administrator</a> with this information.\n" ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	}
	else
	{
	    $wgOut->addHTML( "Successfully inserted/updateed role $rname<br />\n" ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	}
    }

    private function delRole()
    {
	global $wgRequest, $wgOut, $wgDBserver, $wgServer, $wgUser ;

	$allowed = $wgUser->isAllowed( 'cedar_admin' ) ;

	if( !$allowed )
	{
	    $wgOut->addHTML( "You are not allowed to create new CEDAR contacts<br />\n" ) ;
	    return ;
	}

	$wgOut->addHTML( "<h1>Delete a Role</h1>\n" ) ;

	$rid = $wgRequest->getText('role_id');
	$answer = $wgRequest->getText('answer');
	if( $answer && $answer == "yes" )
	{
	    // Connect to the CEDARCATALOG database
	    $dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	    if( !$dbh || !$dbh->isOpen() )
	    {
		$wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
		if( $dbh )
		{
		    $db_error = $dbh->lastError() ;
		    $wgOut->addHTML( "$db_error<br /> \n" ) ;
		}
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		return ;
	    }
	    $sql = "DELETE FROM tbl_role WHERE role_id = $rid;" ;
	    $res = $dbh->query( $sql ) ;
	    if( !$res )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
		$wgOut->addHTML( $db_error ) ;
		$wgOut->addHTML( "<BR />\n" ) ;
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		return ;
	    }
	    $wgOut->addHTML( "Successfully delete the role<br />\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	else if( $answer && $answer == "no" )
	{
	    $this->displayList() ;
	    return ;
	}

	// Connect to the CEDARCATALOG database
	$dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	if( !$dbh || !$dbh->isOpen() )
	{
	    $wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
	    if( $dbh )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "$db_error<br /> \n" ) ;
	    }
	    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	$sql = "SELECT DISTINCT p.user_id, r.role_name FROM tbl_person_role pr, tbl_role r, tbl_person p WHERE pr.role_id = $rid AND pr.role_id = r.role_id AND pr.person_id = p.person_id;" ;
	$res = $dbh->query( $sql ) ;
	if( !$res )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<BR />\n" ) ;
	    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	$isfirst = true ;
	while( ( $obj = $dbh->fetchObject( $res ) ) )
	{
	    $uid = $obj->user_id ;
	    $role_name = $obj->role_name ;
	    $user = $wgUser->newFromId( $uid ) ;
	    $user_real_name = $user->getRealName() ;
	    $user_name = $user->getName() ;
	    if( $isfirst )
	    {
		$wgOut->addHTML( "The role $role_name is still referenced by:\n<ul>\n" ) ;
	    }
	    if( !$user_real_name || $user_real_name == "" )
	    {
		$user_real_name = $user_name ;
	    }
	    $wgOut->addHTML( "<li>$user_real_name</li>\n" ) ;
	    $isfirst = false ;
	}
	if( !$isfirst )
	{
	    $wgOut->addHTML( "</ul>\n" ) ;
	    $wgOut->addHTML( "<br /><a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	$wgOut->addHTML( "Are you sure you want to delete this role?\n" ) ;
	$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=delRole&role_id=$rid&answer=yes\">Yes</a>" ) ;
	$wgOut->addHTML( "|<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=delRole&role_id=$rid&answer=no\">No</a><br />\n" ) ;
	return ;
    }

    private function editOrg()
    {
	global $wgRequest, $wgOut, $wgDBserver, $wgServer, $wgUser ;

	$allowed = $wgUser->isAllowed( 'cedar_admin' ) ;

	if( !$allowed )
	{
	    $wgOut->addHTML( "You are not allowed to create new CEDAR contacts<br />\n" ) ;
	    return ;
	}

	$wgOut->addHTML( "<h1>Add/Edit an Organization</h1>\n" ) ;

	$dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	if( !$dbh || !$dbh->isOpen() )
	{
	    $wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
	    if( $dbh )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "$db_error<br /> \n" ) ;
	    }
	    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}

	$oid = $wgRequest->getText('org_id') ;
	$oname = $wgRequest->getText('org_name') ;
	$oac = $wgRequest->getText('org_ac') ;
	$pid = $wgRequest->getText('parent_id') ;
	$ourl = $wgRequest->getText('org_url') ;
	$ologo = $wgRequest->getText('org_logo') ;
	if( $oid && $oid != 0 )
	{
	    if( ( !$oname || $oname == "" )
	        && ( !$oac || $oac == "" )
	        && ( !$pid || $pid == 0 )
	        && ( !$ourl || $ourl == "" )
	        && ( !$ologo || $ologo == "" ) )
	    {
		// Go get the information for this role from db
		$sql = "SELECT organization_name, parent_id, organization_acronym, organization_url, organization_logo FROM tbl_organization WHERE organization_id = $oid;" ;
		$res = $dbh->query( $sql ) ;
		if( !$res )
		{
		    $db_error = $dbh->lastError() ;
		    $wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
		    $wgOut->addHTML( $db_error ) ;
		    $wgOut->addHTML( "<BR />\n" ) ;
		    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		    return ;
		}
		// role_id is uniq so we should get at least one
		$obj = $dbh->fetchObject( $res ) ;
		if( $obj )
		{
		    $oname = $obj->organization_name ;
		    $oac = $obj->organization_acronym ;
		    $pid = $obj->parent_id ;
		    $ourl = $obj->organization_url ;
		    $ologo = $obj->organization_logo ;
		}
	    }
	    // else they've edited the role but something is wrong
	}
	// Display the form
	$wgOut->addHTML( "<form name=\"cedarcontact\" action=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\" method=\"POST\">\n" ) ;
	$wgOut->addHTML( "<input type=\"hidden\" name=\"action\" value=\"submitOrg\">\n" ) ;
	$wgOut->addHTML( "<input type=\"hidden\" name=\"org_id\" value=\"$oid\">\n" ) ;
	$wgOut->addHTML( "<table width=\"400px\" border=\"0\" cellpadding=\"2\" cellspacing=\"2\">\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "Org Name:&nbsp;&nbsp;\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"text\" name=\"org_name\" value=\"$oname\" SIZE=\"30\" />\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "Parent Role:&nbsp;&nbsp;\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<select name=\"parent_id\" size=\"1\">\n" ) ;
	if( !$pid || $pid == 0 ) $pselect = "SELECTED" ;
	$wgOut->addHTML( "  <option value=\"\" $pselect> </option>\n" ) ;
	if( $oid && $oid != 0 ) $where = "WHERE organization_id != $oid" ;
	$sql = "SELECT organization_id,organization_name FROM tbl_organization $where;" ;
	$res = $dbh->query( $sql ) ;
	if( !$res )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<BR />\n" ) ;
	    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	while( $obj = $dbh->fetchObject( $res ) )
	{
	    $porg_name = $obj->organization_name ;
	    $porg_id = $obj->organization_id ;
	    if( $pid && $pid == $porg_id ) $is_selected="SELECTED" ;
	    else $is_selected="" ;
	    $wgOut->addHTML( "  <option value=\"$porg_id\" $is_selected>$porg_name</option>\n" ) ;
	}
	$wgOut->addHTML( "</select>\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "Org Acronym:&nbsp;&nbsp;\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"text\" name=\"org_ac\" value=\"$oac\" SIZE=\"30\" />\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "Org URL:&nbsp;&nbsp;\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"text\" name=\"org_url\" value=\"$ourl\" SIZE=\"30\" />\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "Org Logo URL:&nbsp;&nbsp;\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"text\" name=\"org_logo\" value=\"$ologo\" SIZE=\"30\" />\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"submit\" name=\"submit\" value=\"Submit\">\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"reset\" value=\"Reset\">\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "</table>\n" ) ;
	$wgOut->addHTML( "</form>\n" ) ;
    }

    private function submitOrg()
    {
	global $wgRequest, $wgOut, $wgDBserver, $wgServer, $wgUser ;

	$allowed = $wgUser->isAllowed( 'cedar_admin' ) ;

	if( !$allowed )
	{
	    $wgOut->addHTML( "You are not allowed to create new CEDAR contacts<br />\n" ) ;
	    return ;
	}

	$oid = $wgRequest->getText('org_id') ;
	$oname = $wgRequest->getText('org_name') ;
	$oac = $wgRequest->getText('org_ac') ;
	$pid = $wgRequest->getText('parent_id') ;
	$ourl = $wgRequest->getText('org_url') ;
	$ologo = $wgRequest->getText('org_logo') ;

	if( !$oname || $oname == "" )
	{
	    $this->editOrg() ;
	    return ;
	}

	$dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	if( !$dbh || !$dbh->isOpen() )
	{
	    $wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
	    if( $dbh )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "$db_error<br /> \n" ) ;
	    }
	    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	$org_table = $dbh->tableName( 'tbl_organization' ) ;
	$oid = $dbh->strencode( $oid ) ;
	$oname = $dbh->strencode( $oname ) ;
	$pid = $dbh->strencode( $pid ) ;
	$oac = $dbh->strencode( $oac ) ;
	$ourl = $dbh->strencode( $ourl ) ;
	$ologo = $dbh->strencode( $ologo ) ;

	if( !$oid || $oid == 0 )
	{
	    // adding new org
	    $insert_success = $dbh->insert( $org_table,
		    array(
			'organization_name' => "$oname",
			'parent_id' => "$pid",
			'organization_acronym' => "$oac",
			'organization_url' => "$ourl",
			'organization_logo' => "$ologo",
		    ),
		    __METHOD__
		) ;
	}
	else
	{
	    // updating existing role
	    $insert_success = $dbh->update( $org_table,
		    array(
			'organization_name' => "$oname",
			'parent_id' => "$pid",
			'organization_acronym' => "$oac",
			'organization_url' => "$ourl",
			'organization_logo' => "$ologo",
		    ), array( /* WHERE */
			    'organization_id' => $oid
		    ), __METHOD__
		) ;
	}
	if( $insert_success == false )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Failed to insert/update org $oname<br />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    $wgOut->addHTML( "Please contact <a href=\"mailto:cedar_db@hao.ucar.edu\">Cedar Administrator</a> with this information.\n" ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	}
	else
	{
	    $wgOut->addHTML( "Successfully inserted/updateed org $oname<br />\n" ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	}
    }

    private function delOrg()
    {
	global $wgRequest, $wgOut, $wgDBserver, $wgServer, $wgUser ;

	$allowed = $wgUser->isAllowed( 'cedar_admin' ) ;

	if( !$allowed )
	{
	    $wgOut->addHTML( "You are not allowed to create new CEDAR contacts<br />\n" ) ;
	    return ;
	}

	$wgOut->addHTML( "<h1>Delete an Organization</h1>\n" ) ;

	$oid = $wgRequest->getText('org_id');
	$answer = $wgRequest->getText('answer');
	if( $answer && $answer == "yes" )
	{
	    // Connect to the CEDARCATALOG database
	    $dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	    if( !$dbh || !$dbh->isOpen() )
	    {
		$wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
		if( $dbh )
		{
		    $db_error = $dbh->lastError() ;
		    $wgOut->addHTML( "$db_error<br /> \n" ) ;
		}
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		return ;
	    }
	    $sql = "DELETE FROM tbl_organization WHERE organization_id = $oid;" ;
	    $res = $dbh->query( $sql ) ;
	    if( !$res )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
		$wgOut->addHTML( $db_error ) ;
		$wgOut->addHTML( "<BR />\n" ) ;
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		return ;
	    }
	    $wgOut->addHTML( "Successfully deleted the organization<br />\n" ) ;
	    $sql = "UPDATE tbl_organization SET parent_id=DEFAULT WHERE parent_id = $oid;" ;
	    $res = $dbh->query( $sql ) ;
	    if( !$res )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
		$wgOut->addHTML( $db_error ) ;
		$wgOut->addHTML( "<BR />\n" ) ;
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		return ;
	    }
	    $wgOut->addHTML( "Successfully updated children orgs<br />\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	else if( $answer && $answer == "no" )
	{
	    $this->displayList() ;
	    return ;
	}

	// Connect to the CEDARCATALOG database
	$dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	if( !$dbh || !$dbh->isOpen() )
	{
	    $wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
	    if( $dbh )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "$db_error<br /> \n" ) ;
	    }
	    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	$sql = "SELECT DISTINCT p.user_id, o.organization_name FROM tbl_person p, tbl_person_role pr, tbl_organization o WHERE pr.organization_id = $oid AND o.organization_id = pr.organization_id AND pr.person_id = p.person_id;" ;
	$res = $dbh->query( $sql ) ;
	if( !$res )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<BR />\n" ) ;
	    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	$isfirst = true ;
	while( ( $obj = $dbh->fetchObject( $res ) ) )
	{
	    $uid = $obj->user_id ;
	    $org_name = $obj->organization_name ;
	    $user = $wgUser->newFromId( $uid ) ;
	    $user_real_name = $user->getRealName() ;
	    $user_name = $user->getName() ;
	    if( $isfirst )
	    {
		$wgOut->addHTML( "The organization $org_name is still referenced by:\n<ul>\n" ) ;
	    }
	    if( !$user_real_name || $user_real_name == "" )
	    {
		$user_real_name = $user_name ;
	    }
	    $wgOut->addHTML( "<li>$user_real_name</li>\n" ) ;
	    $isfirst = false ;
	}
	if( !$isfirst )
	{
	    $wgOut->addHTML( "</ul>\n" ) ;
	    $wgOut->addHTML( "<br /><a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	$wgOut->addHTML( "Are you sure you want to delete this organization?\n" ) ;
	$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=delOrg&org_id=$oid&answer=yes\">Yes</a>" ) ;
	$wgOut->addHTML( "|<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=delOrg&org_id=$oid&answer=no\">No</a><br />\n" ) ;
	return ;
    }

    private function editPerson()
    {
	global $wgRequest, $wgOut, $wgDBserver, $wgServer, $wgUser ;

	$allowed = $wgUser->isAllowed( 'cedar_admin' ) ;

	if( !$allowed )
	{
	    $wgOut->addHTML( "You are not allowed to create new CEDAR contacts<br />\n" ) ;
	    return ;
	}

	$wgOut->addHTML( "<h1>Add/Edit a Person</h1>\n" ) ;

	$dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	if( !$dbh || !$dbh->isOpen() )
	{
	    $wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
	    if( $dbh )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "$db_error<br /> \n" ) ;
	    }
	    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}

	$pid = $wgRequest->getText('person_id') ;
	$uid = $wgRequest->getText('user_id') ;
	$real_name = "" ;
	$email = "" ;
	$uphone = $wgRequest->getText('user_phone') ;
	$ufax = $wgRequest->getText('user_fax') ;
	$uurl = $wgRequest->getText('user_url') ;
	if( $pid && $pid != 0 )
	{
	    if( ( !$uid || $uid == 0 )
	        && ( !$uphone || $uphone == "" )
	        && ( !$uurl || $uurl == "" )
		&& ( !$ufax || $ufax == 0 ) )
	    {
		// Go get the information for this person from db
		$sql = "SELECT user_id, user_phone, user_fax, user_url FROM tbl_person WHERE person_id = $pid;" ;
		$res = $dbh->query( $sql ) ;
		if( !$res )
		{
		    $db_error = $dbh->lastError() ;
		    $wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
		    $wgOut->addHTML( $db_error ) ;
		    $wgOut->addHTML( "<BR />\n" ) ;
		    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		    return ;
		}
		// role_id is uniq so we should get at least one
		$obj = $dbh->fetchObject( $res ) ;
		if( $obj )
		{
		    $uid = $obj->user_id ;
		    $uphone = $obj->user_phone ;
		    $ufax = $obj->user_fax ;
		    $uurl = $obj->user_url ;
		    $user = $wgUser->newFromId( $uid ) ;
		    $real_name = $user->getRealName() ;
		    if( !$real_name || $real_name == "" )
		    {
			$real_name = $user->getName() ;
		    }
		    $email = $user->getEmail() ;
		}
	    }
	}
	// Display the form
	$wgOut->addHTML( "<form name=\"cedarcontact\" action=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\" method=\"POST\">\n" ) ;
	$wgOut->addHTML( "<input type=\"hidden\" name=\"action\" value=\"submitPerson\">\n" ) ;
	$wgOut->addHTML( "<input type=\"hidden\" name=\"person_id\" value=\"$pid\">\n" ) ;
	$wgOut->addHTML( "<input type=\"hidden\" name=\"user_id\" value=\"$uid\">\n" ) ;
	$wgOut->addHTML( "<table width=\"400px\" border=\"0\" cellpadding=\"2\" cellspacing=\"2\">\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "User Name:&nbsp;&nbsp;\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	if( $real_name != "" )
	{
	    $wgOut->addHTML( "<input type=\"text\" DISABLED name=\"real_name\" value=\"$real_name\" SIZE=\"30\" />\n" ) ;
	}
	else
	{
	    if( $uid && $uid != 0 ) $where = "WHERE u.user_id != $uid" ;
	    else $where = "WHERE u.user_id not in ( SELECT user_id from CEDARCATALOG.tbl_person )" ;
	    $sql = "SELECT u.user_id,u.user_name,u.user_real_name FROM wikidb.user u $where GROUP BY user_real_name;";
	    $dbw =& wfGetDB( DB_MASTER );
	    $res = $dbw->query( $sql ) ;
	    if( !$res )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
		$wgOut->addHTML( $db_error ) ;
		$wgOut->addHTML( "<BR />\n" ) ;
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		return ;
	    }
	    $wgOut->addHTML( "<select name=\"user_id\" size=\"1\">\n" ) ;
	    $wgOut->addHTML( "  <option value=\"0\" SELECTED>Select Person</option>\n" ) ;
	    while( $obj = $dbh->fetchObject( $res ) )
	    {
		$suid = $obj->user_id ;
		$sreal_name = $obj->user_real_name ;
		$sname = $obj->user_name ;
		if( !$sreal_name || $sreal_name == "" )
		{
		    $sreal_name = $sname ;
		}
		$wgOut->addHTML( "  <option value=\"$suid\">$sreal_name</option>\n" ) ;
	    }
	    $wgOut->addHTML( "</select>\n" ) ;
	}
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "Phone:&nbsp;&nbsp;\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"text\" name=\"user_phone\" value=\"$uphone\" SIZE=\"30\" />\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "Fax:&nbsp;&nbsp;\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"text\" name=\"user_fax\" value=\"$ufax\" SIZE=\"30\" />\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "URL:&nbsp;&nbsp;\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"text\" name=\"user_url\" value=\"$uurl\" SIZE=\"30\" />\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"submit\" name=\"submit\" value=\"Submit\">\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"reset\" value=\"Reset\">\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "</table>\n" ) ;
	$wgOut->addHTML( "</form>\n" ) ;
    }

    private function submitPerson()
    {
	global $wgRequest, $wgOut, $wgDBserver, $wgServer, $wgUser ;

	$allowed = $wgUser->isAllowed( 'cedar_admin' ) ;

	if( !$allowed )
	{
	    $wgOut->addHTML( "You are not allowed to create new CEDAR contacts<br />\n" ) ;
	    return ;
	}

	$pid = $wgRequest->getText('person_id') ;
	$uid = $wgRequest->getText('user_id') ;
	$uphone = $wgRequest->getText('user_phone') ;
	$ufax = $wgRequest->getText('user_fax') ;
	$uurl = $wgRequest->getText('user_url') ;
	
	if( !$uid || $uid == 0 )
	{
	    $wgOut->addHTML( "<span style=\"color:red;\">You must select a person from the list<br /><br />\n" ) ;
	    $this->editPerson() ;
	    return ;
	}

	$dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	if( !$dbh || !$dbh->isOpen() )
	{
	    $wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
	    if( $dbh )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "$db_error<br /> \n" ) ;
	    }
	    $wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	$person_table = $dbh->tableName( 'tbl_person' ) ;
	$pid = $dbh->strencode( $pid ) ;
	$uid = $dbh->strencode( $uid ) ;
	$uphone = $dbh->strencode( $uphone ) ;
	$ufax = $dbh->strencode( $ufax ) ;
	$uurl = $dbh->strencode( $uurl ) ;

	if( !$pid || $pid == 0 )
	{
	    // adding new person
	    $insert_success = $dbh->insert( $person_table,
		    array(
			'user_id' => "$uid",
			'user_phone' => "$uphone",
			'user_fax' => "$ufax",
			'user_url' => "$uurl",
		    ),
		    __METHOD__
		) ;
	}
	else
	{
	    // updating existing role
	    $insert_success = $dbh->update( $person_table,
		    array(
			'user_phone' => "$uphone",
			'user_fax' => "$ufax",
			'user_url' => "$uurl",
		    ), array( /* WHERE */
			    'person_id' => $pid
		    ), __METHOD__
		) ;
	}
	if( $insert_success == false )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Failed to insert/update person<br />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    $wgOut->addHTML( "Please contact <a href=\"mailto:cedar_db@hao.ucar.edu\">Cedar Administrator</a> with this information.\n" ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	}
	else
	{
	    $wgOut->addHTML( "Successfully inserted/updateed person<br />\n" ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	}
    }

    private function addPRole()
    {
	global $wgRequest, $wgOut, $wgDBserver, $wgServer, $wgUser ;

	$allowed = $wgUser->isAllowed( 'cedar_admin' ) ;

	if( !$allowed )
	{
	    $wgOut->addHTML( "You are not allowed to create new CEDAR contacts<br />\n" ) ;
	    return ;
	}

	$wgOut->addScript( "<script language=\"javascript\">

	function instrSelect() {

	if (document.cedarcontact.kr.checked == true )
	{
	  document.cedarcontact.kinst.disabled=false;
	  document.cedarcontact.fileregex.disabled=true;
	}
	}

	function fileSelect() {

	if (document.cedarcontact.pfr.checked == true )
	{
	  document.cedarcontact.kinst.disabled=true;
	  document.cedarcontact.fileregex.disabled=false;
	}
	}

	</script>\n" ) ;
	$wgOut->addHTML( "<h1>Add a Role for Person</h1>\n" ) ;

	// Connect to the CEDARCATALOG database
	$dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	if( !$dbh || !$dbh->isOpen() )
	{
	    $wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
	    if( $dbh )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "$db_error<br /> \n" ) ;
	    }
	    return ;
	}

	$pid = $wgRequest->getText( "person_id" ) ;

	$sql = "SELECT * FROM tbl_instrument WHERE KINST not in ( SELECT context_id from tbl_person_role WHERE person_id = $pid AND role_id = 1 ) GROUP BY KINST" ;
	$res = $dbh->query( $sql ) ;
	if( !$res )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<BR />\n" ) ;
	    return ;
	}

	$wgOut->addHTML( "<form name=\"cedarcontact\" action=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\" method=\"POST\">\n" ) ;
	$wgOut->addHTML( "<input type=\"hidden\" name=\"action\" value=\"submitPRole\">\n" ) ;
	$wgOut->addHTML( "<input type=\"hidden\" name=\"person_id\" value=\"$pid\">\n" ) ;
	$wgOut->addHTML( "<table width=\"700px\" border=\"0\" cellpadding=\"2\" cellspacing=\"2\">\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"vertical-align:top;text-align:center;\">\n" ) ;
	$wgOut->addHTML( "<input onClick=\"instrSelect()\" type=\"radio\" id=\"kr\" name=\"my_role_id\" value=\"1\">\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "Contact for Instrument:<br />\n" ) ;
	$wgOut->addHTML( "<select id=\"kinst\" name=\"kinst\" size=\"1\" disabled>\n" ) ;
	$wgOut->addHTML( "  <option value=\"\">&lt;Select Instrument&gt;</option>\n" ) ;

	while( ( $obj = $dbh->fetchObject( $res ) ) )
	{
	    $kinst = $obj->KINST ;
	    $prefix = $obj->PREFIX ;
	    $name = $obj->INST_NAME ;
	    $wgOut->addHTML( "  <option value=\"$kinst\">$kinst - $prefix - $name</option>\n" ) ;
	}

	$wgOut->addHTML( "</select>\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"vertical-align:top;text-align:center;\">\n" ) ;
	$wgOut->addHTML( "<input onClick=\"fileSelect()\" type=\"radio\" id=\"pfr\" name=\"my_role_id\" value=\"2\">\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "Contact for Protected Files:<br />\n" ) ;
	$wgOut->addHTML( "<input disabled type=\"text\" width=\"60\" id=\"fileregex\" name=\"fileregex\">\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "<tr>\n" ) ;
	$wgOut->addHTML( "<td style=\"width:20px;text-align:right;\">\n" ) ;
	$wgOut->addHTML( "&nbsp;\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:right;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"submit\" name=\"submit\" value=\"Submit\">\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "<td style=\"text-align:left;\">\n" ) ;
	$wgOut->addHTML( "<input type=\"reset\" value=\"Reset\">\n" ) ;
	$wgOut->addHTML( "</td>\n" ) ;
	$wgOut->addHTML( "</tr>\n" ) ;
	$wgOut->addHTML( "</table>\n" ) ;
	$wgOut->addHTML( "</form>\n" ) ;
    }

    private function submitPRole()
    {
	global $wgRequest, $wgOut, $wgDBserver, $wgServer, $wgUser ;

	$allowed = $wgUser->isAllowed( 'cedar_admin' ) ;

	if( !$allowed )
	{
	    $wgOut->addHTML( "You are not allowed to create new CEDAR contacts<br />\n" ) ;
	    return ;
	}

	// Connect to the CEDARCATALOG database
	$dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	if( !$dbh || !$dbh->isOpen() )
	{
	    $wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
	    if( $dbh )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "$db_error<br /> \n" ) ;
	    }
	    return ;
	}

	$pid = $dbh->strencode( $wgRequest->getText( "person_id" ) ) ;
	$rid = $dbh->strencode( $wgRequest->getText( "my_role_id" ) ) ;
	$kinst = $dbh->strencode( $wgRequest->getText( "kinst" ) ) ;
	$exp = $dbh->strencode( $wgRequest->getText( "fileregex" ) ) ;
	$pr_table = $dbh->tableName( 'tbl_person_role' ) ;

	if( $rid == 1 && $kinst == "" )
	{
	    $wgOut->addHTML( "Must specify an instrument for data contact<br />\n" ) ;
	    return ;
	}
	else if( $rid == 2 && $exp == "" )
	{
	    $wgOut->addHTML( "Must specify an expression for protexted file contact<br />\n" ) ;
	    return ;
	}
	else if( $rid != 1 && $rid != 2 )
	{
	    $wgOut->addHTML( "Unknow role type $rid<br />\n" ) ;
	    return ;
	}

	if( $rid == 1 ) $cid = $kinst ;
	else if( $rid == 2 ) $cid = $exp ;

	// adding new role
	$insert_success = $dbh->insert( $pr_table,
		array(
		    'person_id' => "$pid",
		    'role_id' => "$rid",
		    'context_id' => "$cid",
		),
		__METHOD__
	    ) ;

	if( $insert_success == false )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Failed to add contact role<br />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    $wgOut->addHTML( "Please contact <a href=\"mailto:cedar_db@hao.ucar.edu\">Cedar Administrator</a> with this information.\n" ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	}
	else
	{
	    $wgOut->addHTML( "Successfully add contact role<br />\n" ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	}
    }

    private function delPRole()
    {
	global $wgRequest, $wgOut, $wgDBserver, $wgServer, $wgUser ;

	$allowed = $wgUser->isAllowed( 'cedar_admin' ) ;

	if( !$allowed )
	{
	    $wgOut->addHTML( "You are not allowed to create new CEDAR contacts<br />\n" ) ;
	    return ;
	}

	$wgOut->addHTML( "<h1>Delete a Person's Role</h1>\n" ) ;

	$prid = $wgRequest->getText('person_role_id');
	$answer = $wgRequest->getText('answer');
	if( $answer && $answer == "yes" )
	{
	    // Connect to the CEDARCATALOG database
	    $dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	    if( !$dbh || !$dbh->isOpen() )
	    {
		$wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
		if( $dbh )
		{
		    $db_error = $dbh->lastError() ;
		    $wgOut->addHTML( "$db_error<br /> \n" ) ;
		}
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		return ;
	    }
	    $sql = "DELETE FROM tbl_person_role WHERE person_role_id = $prid;" ;
	    $res = $dbh->query( $sql ) ;
	    if( !$res )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
		$wgOut->addHTML( $db_error ) ;
		$wgOut->addHTML( "<BR />\n" ) ;
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		return ;
	    }
	    $wgOut->addHTML( "Successfully deleted the role<br />\n" ) ;
	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	else if( $answer && $answer == "no" )
	{
	    $this->displayList() ;
	    return ;
	}

	$wgOut->addHTML( "Are you sure you want to delete this role?\n" ) ;
	$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=delPRole&person_role_id=$prid&answer=yes\">Yes</a>" ) ;
	$wgOut->addHTML( "|<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=delPRole&person_role_id=$prid&answer=no\">No</a><br />\n" ) ;
    }

    private function delPerson()
    {
	global $wgRequest, $wgOut, $wgDBserver, $wgServer, $wgUser ;

	$allowed = $wgUser->isAllowed( 'cedar_admin' ) ;

	if( !$allowed )
	{
	    $wgOut->addHTML( "You are not allowed to create new CEDAR contacts<br />\n" ) ;
	    return ;
	}

	$wgOut->addHTML( "<h1>Delete a Person</h1>\n" ) ;

	$pid = $wgRequest->getText('person_id');
	$answer = $wgRequest->getText('answer');
	if( $answer && $answer == "yes" )
	{
	    // Connect to the CEDARCATALOG database
	    $dbh = new DatabaseMysql( $wgDBserver, $this->dbuser, $this->dbpwd, "CEDARCATALOG" ) ;
	    if( !$dbh || !$dbh->isOpen() )
	    {
		$wgOut->addHTML( "Unable to connect to the CEDAR Catalog database<br />\n" ) ;
		if( $dbh )
		{
		    $db_error = $dbh->lastError() ;
		    $wgOut->addHTML( "$db_error<br /> \n" ) ;
		}
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		return ;
	    }

	    $sql = "DELETE FROM tbl_person WHERE person_id = $pid;" ;
	    $res = $dbh->query( $sql ) ;
	    if( !$res )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
		$wgOut->addHTML( $db_error ) ;
		$wgOut->addHTML( "<BR />\n" ) ;
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		return ;
	    }
	    $wgOut->addHTML( "Successfully deleted the person<br />\n" ) ;

	    $sql = "DELETE FROM tbl_person_role WHERE person_id = $pid;" ;
	    $res = $dbh->query( $sql ) ;
	    if( !$res )
	    {
		$db_error = $dbh->lastError() ;
		$wgOut->addHTML( "Unable to query the CEDAR Catalog database<BR />\n" ) ;
		$wgOut->addHTML( $db_error ) ;
		$wgOut->addHTML( "<BR />\n" ) ;
		$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
		return ;
	    }
	    $wgOut->addHTML( "Successfully deleted the person's roles<br />\n" ) ;

	    $wgOut->addHTML( "<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts\">Return to Contacts Page</a>" ) ;
	    return ;
	}
	else if( $answer && $answer == "no" )
	{
	    $this->displayList() ;
	    return ;
	}

	$wgOut->addHTML( "Are you sure you want to delete this role?\n" ) ;
	$wgOut->addHTML( " <a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=delPerson&person_id=$pid&answer=yes\">Yes</a>" ) ;
	$wgOut->addHTML( "|<a href=\"$wgServer/wiki/index.php/Special:Cedar_Contacts?action=delPerson&person_id=$pid&answer=no\">No</a><br />\n" ) ;
	return ;
    }
}
?>
