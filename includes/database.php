<?php
/*
Template Name: db-settings-18.php
*/
?>
<?php 
global $wpdb; 
$ostemail = $wpdb->prefix . "ost_emailtemp"; 
$adminreply=$wpdb->get_row("SELECT id,name,$ostemail.subject,$ostemail.text,created,updated FROM $ostemail where name = 'Admin-Response'"); 
$adminreply=$adminreply->text;
$form_admintreply_subject=$adminreply->subject;
$arname='Admin-Response';

$postsubmail=$wpdb->get_row("SELECT id,name,$ostemail.subject,text,created,updated FROM $ostemail where name = 'Admin-Response'"); 
$postsubmail=$postsubmail->text;

$newticket=$wpdb->get_row("SELECT id,name,$ostemail.subject,$ostemail.text,created,updated FROM $ostemail where name = 'New-Ticket'"); $form_newticket_subject=$newticket->subject;
$newticket=$newticket->text; 
$ntname='New-Ticket';

$user_name=$current_user->user_login; 
$e_address=$current_user->user_email;
/*Add user id of ticket instead of wordpress start here */
$user_id = $ost_wpdb->get_var("SELECT user_id FROM ".$keyost_prefix."user_email WHERE `address` = '".$e_address."'");
/*Add user id of ticket instead of wordpress end here*/
$ost_wpdb = new wpdb($username, $password, $database, $host);
        global $current_user;
		
        $config_table = $keyost_prefix . "config";
        $dept_table = $keyost_prefix . "department";
        $topic_table = $keyost_prefix . "help_topic";
        $ticket_table = $keyost_prefix . "ticket";
        $ticket_event_table = $keyost_prefix . "ticket_event";
        $priority_table = $keyost_prefix . "ticket_priority";
        $thread_table = $keyost_prefix . "ticket_thread";
        $ticket_cdata = $keyost_prefix . "ticket__cdata";
        $ost_user = $keyost_prefix . "user";
		$ost_email = $keyost_prefix . "email";
        $ost_staff = $keyost_prefix . "staff";
        $ost_useremail = $keyost_prefix . "user_email";
        $ost_ticket_attachment = $keyost_prefix . "ticket_attachment";
		$ost_ticket_status=$keyost_prefix."ticket_status";		
        $ost_file = $keyost_prefix . "file";
if($keyost_version==194 || $keyost_version==195 || $keyost_version==1951){
$getNumOpenTickets=$ost_wpdb->get_var("SELECT COUNT(*) FROM $ticket_table INNER JOIN $ost_ticket_status ON $ost_ticket_status.id=$ticket_table.status_id WHERE user_id='$user_id' and $ost_ticket_status.state='open'"); 
$ticket_count=$ost_wpdb->get_var("SELECT COUNT(*) FROM $ticket_table WHERE user_id='$user_id'"); 
$ticket_count_open=$ost_wpdb->get_var("SELECT COUNT(*) FROM $ticket_table INNER JOIN $ost_ticket_status ON $ost_ticket_status.id=$ticket_table.status_id WHERE user_id='$user_id' and $ost_ticket_status.state='open'"); 
$ticket_count_closed=$ost_wpdb->get_var("SELECT COUNT(*) FROM $ticket_table INNER JOIN $ost_ticket_status ON $ost_ticket_status.id=$ticket_table.status_id WHERE user_id='$user_id' and $ost_ticket_status.state='closed'"); 
}else{
$getNumOpenTickets=$ost_wpdb->get_var("SELECT COUNT(*) FROM $ticket_table WHERE user_id='$user_id' and status='open'"); 
$ticket_count=$ost_wpdb->get_var("SELECT COUNT(*) FROM $ticket_table WHERE user_id='$user_id'"); 
$ticket_count_open=$ost_wpdb->get_var("SELECT COUNT(*) FROM $ticket_table WHERE user_id='$user_id' and status='open'"); 
$ticket_count_closed=$ost_wpdb->get_var("SELECT COUNT(*) FROM $ticket_table WHERE user_id='$user_id' and status='closed'"); 
}
//////Ticket Info
if($keyost_version==194 || $keyost_version==195 || $keyost_version==1951){
if(isset($ticket)){
$ticketinfo=$ost_wpdb->get_row("SELECT $topic_table.topic,$ticket_table.user_id,$ost_ticket_status.state as status,$ticket_table.number,$ticket_table.created,$ticket_table.ticket_id,$ticket_table.isanswered,$ost_user.name,$dept_table.dept_name,$ticket_cdata.priority,$ticket_cdata.subject,$ost_useremail.address FROM $ticket_table INNER JOIN $dept_table ON $dept_table.dept_id=$ticket_table.dept_id LEFT JOIN $topic_table ON $topic_table.topic_id=$ticket_table.topic_id INNER JOIN $ost_user ON $ost_user.id=$ticket_table.user_id INNER JOIN $ost_ticket_status ON $ost_ticket_status.id=$ticket_table.status_id INNER JOIN $ost_useremail ON $ost_useremail.user_id=$ticket_table.user_id LEFT JOIN $ticket_cdata on $ticket_cdata.ticket_id = $ticket_table.ticket_id WHERE `number` ='$ticket'");
}} else {
if(isset($ticket)){
$ticketinfo=$ost_wpdb->get_row("SELECT $topic_table.topic,$ticket_table.user_id,$ticket_table.number,$ticket_table.created,$ticket_table.ticket_id,$ticket_table.status,$ticket_table.isanswered,$ost_user.name,$dept_table.dept_name,$ticket_cdata.priority,$ticket_cdata.priority_id,$ticket_cdata.subject,$ost_useremail.address FROM $ticket_table INNER JOIN $dept_table ON $dept_table.dept_id=$ticket_table.dept_id LEFT JOIN $topic_table ON $topic_table.topic_id=$ticket_table.topic_id INNER JOIN $ost_user ON $ost_user.id=$ticket_table.user_id INNER JOIN $ost_useremail ON $ost_useremail.user_id=$ticket_table.user_id LEFT JOIN $ticket_cdata on $ticket_cdata.ticket_id = $ticket_table.ticket_id WHERE `number` ='$ticket'");
}
}
//////Thread Info
if(isset($ticket)){
$threadinfo=$ost_wpdb->get_results("SELECT $ost_useremail.address,$thread_table.created,$thread_table.id,$thread_table.ticket_id,$thread_table.thread_type,$thread_table.body,$thread_table.poster 
	FROM $thread_table 
	inner join $ticket_table on $thread_table.ticket_id = $ticket_table.ticket_id 
	inner join ".$keyost_prefix."user_email on ".$keyost_prefix."user_email.user_id = $ticket_table.user_id
	where number = '$ticket' 
	ORDER BY  $thread_table.id ASC"); 
}
$search="";
if(isset($_REQUEST['search'])){
$search=@$_REQUEST['tq'];
}
if(isset($_POST['action']))
$arr = explode('.', $_POST['action']);
if(!@$status_opt && (@$status_opt!="all")) {
	if($ticket_count_open > 0)
		@$status_opt='open';
	else
		@$status_opt='closed';
    }
if(!$status_opt && ($status_opt=="all")) 
	$status_opt='';
if($status_opt=="open"){
	$status_opt='open';
    }
elseif($status_opt=="closed") {
	$status_opt='closed';
	}	
if($user_id!=""){
if($keyost_version==194 || $keyost_version==195 || $keyost_version==1951){
    $sql="";
    $sql="SELECT $topic_table.topic,$ost_ticket_status.state as status,$ticket_table.user_id,$ticket_table.number,$ticket_table.created, $ticket_table.updated, $ticket_table.ticket_id,$ticket_table.isanswered,$ticket_cdata.subject,$ticket_cdata.priority, $dept_table.dept_name
    FROM $ticket_table
    LEFT JOIN $ticket_cdata ON $ticket_cdata.ticket_id = $ticket_table.ticket_id
    INNER JOIN $dept_table ON $dept_table.dept_id = $ticket_table.dept_id
    LEFT JOIN $topic_table ON $topic_table.topic_id=$ticket_table.topic_id
    INNER JOIN $ost_ticket_status ON $ost_ticket_status.id=$ticket_table.status_id
    WHERE $ticket_table.user_id =$user_id";
}else{
$sql="";
$sql="SELECT $topic_table.topic,$ticket_table.user_id,$ticket_table.number,$ticket_table.created, $ticket_table.updated, $ticket_table.ticket_id, $ticket_table.status,$ticket_table.isanswered,$ticket_cdata.subject,$ticket_cdata.priority_id, $dept_table.dept_name
      FROM $ticket_table
      LEFT JOIN $ticket_cdata ON $ticket_cdata.ticket_id = $ticket_table.ticket_id
      LEFT JOIN $topic_table ON $topic_table.topic_id=$ticket_table.topic_id
      INNER JOIN $dept_table ON $dept_table.dept_id = $ticket_table.dept_id WHERE $ticket_table.user_id =$user_id";
}
if(@$category && (@$category!="all"))
$sql.=" and $topic_table.topic_id = '".$category."'";
if($status_opt && ($status_opt!="all") && $search==""){
if($keyost_version==194 || $keyost_version==195 || $keyost_version==1951)
	$sql.=" and $ost_ticket_status.state = '".$status_opt."'";
else	
	$sql.=" and $ticket_table.status = '".$status_opt."'";
}
if(@$search && ($search!=""))
    $sql.=" and ($ticket_table.number like '%".$search."%' or $ost_ticket_status.state like '%".$search."%' or $ticket_cdata.subject like '%".$search."%' or $dept_table.dept_name like '%".$search."%')";
    $sql.=" GROUP BY $ticket_table.ticket_id";  
if(isset($_POST['action']) && $arr[0]=='ascen')
    $sql.=" ORDER BY $arr[1] ASC, $ticket_table.updated ASC";
else if(isset($_POST['action']) && $arr[0]=='desc')
    $sql.=" ORDER BY $arr[1] DESC, $ticket_table.updated DESC";
else
    $sql.=" ORDER BY $ticket_table.ticket_id DESC";
    @$numrows=count($ost_wpdb->get_results($sql)); 
    $rowsperpage = 7; 
    $totalpages = ceil($numrows / $rowsperpage);  
if (isset($_REQUEST['currentpage']) && is_numeric($_REQUEST['currentpage'])) { 
    $currentpage = (int) $_GET['currentpage']; 
} else { 
    $currentpage = 1; 
} 
if ($currentpage > $totalpages) { 
    $currentpage = $totalpages; 
} 
if ($currentpage < 1) { 
    $currentpage = 1; 
} 
$offset = ($currentpage - 1) * $rowsperpage; 
$sql.=" LIMIT $offset, $rowsperpage";  
$list_opt = $ost_wpdb->get_results($sql); 
}
?>