<?php
include ("../lib/admin.defines.php");
include ("../lib/admin.module.access.php");
include ("../lib/Form/Class.FormHandler.inc.php");
include ("./form_data/FG_var_moneysituation.inc");
include ("../lib/admin.smarty.php");

if (! has_rights (ACX_BILLING)){ 
	   Header ("HTTP/1.0 401 Unauthorized");
	   Header ("Location: PP_error.php?c=accessdenied");	   
	   die();	   
}


$HD_Form -> setDBHandler (DbConnect());


$HD_Form -> init();


if ($id!='' || !is_null($id)){	
	$HD_Form -> FG_EDITION_CLAUSE = str_replace("%id", "$id", $HD_Form -> FG_EDITION_CLAUSE);	
}


if (!isset($form_action))  $form_action="list"; //ask-add
if (!isset($action)) $action = $form_action;


$list = $HD_Form -> perform_action($form_action);



// #### HEADER SECTION
$smarty->display('main.tpl');

// #### HELP SECTION
echo $CC_help_money_situation;



// #### TOP SECTION PAGE
$HD_Form -> create_toppage ($form_action);


// #### CREATE FORM OR LIST
//$HD_Form -> CV_TOPVIEWER = "menu";
if (strlen($_GET["menu"])>0) $_SESSION["menu"] = $_GET["menu"];

$HD_Form -> create_form ($form_action, $list, $id=null) ;


// SELECT ROUND(SUM(credit)) from cc_card ;
$instance_table = new Table("cc_card", "ROUND(SUM(credit))");
$total_credits = $instance_table -> Get_list ($HD_Form -> DBHandle, null, null, null, null, null, null, null);
// SELECT SUM(t1.credit) from  cc_logrefill as t1, cc_card as t2 where t1.card_id = t2.id;
$instance_table = new Table("cc_logrefill as t1, cc_card as t2", "SUM(t1.credit)");
$total_refills = $instance_table -> Get_list ($HD_Form -> DBHandle, "t1.card_id = t2.id", null, null, null, null, null, null);
// SELECT SUM(payment) from cc_logpayment as t1 ,cc_card as t2 where t1.card_id=t2.id;
$instance_table = new Table("cc_logpayment as t1 ,cc_card as t2", "SUM(payment)");
$total_payments = $instance_table -> Get_list ($HD_Form -> DBHandle, "t1.card_id=t2.id", null, null, null, null, null, null);
// SELECT SUM(amount) from cc_charge as t1, cc_card as t2 where t1.id_cc_card=t2.id;
$instance_table = new Table("cc_charge as t1, cc_card as t2", "SUM(amount)");
$total_charges = $instance_table -> Get_list ($HD_Form -> DBHandle, "t1.id_cc_card=t2.id", null, null, null, null, null, null);
$total_to_pay = ($total_refills[0][0] + $total_charges[0][0]) - $total_payments[0][0];
?>
<br/>
<table border="1" cellpadding="4" cellspacing="2" width="90%" align="center" class="bgcolor_017" >		
	<tr>
		<td>		
			<table border="2" cellpadding="3" cellspacing="5" width="450" align="right" class="bgcolor_018">		
				<tr class="form_head">                   					
					<td width="20%" align="center" class="tableBodyRight" style="padding: 2px;"><strong><?php echo gettext("TOTAL CREDIT");?></strong></td>
					<td width="20%" align="center" class="tableBodyRight" style="padding: 2px;"><strong><?php echo gettext("TOTAL REFILL");?></strong></td>
					<td width="20%" align="center" class="tableBodyRight" style="padding: 2px;"><strong><?php echo gettext("TOTAL CHARGES");?></strong></td>
					<td width="20%" align="center" class="tableBodyRight" style="padding: 2px;"><strong><?php echo gettext("TOTAL PAYMENT");?></strong></td>
					<td width="20%" align="center" class="tableBodyRight" style="padding: 2px;"><strong><?php echo gettext("TOTAL TOPAY");?></strong></td>
				</tr>
				<tr>
					<td valign="top" align="center" class="tableBody" bgcolor="white"><b><?php echo $total_credits[0][0]; ?></b></td>
					<td valign="top" align="center" class="tableBody" bgcolor="white"><b><?php echo $total_refills[0][0]; ?></b></td>
					<td valign="top" align="center" class="tableBody" bgcolor="white"><b><?php echo $total_charges[0][0]; ?></b></td>
					<td valign="top" align="center" class="tableBody" bgcolor="#DD4444"><b><?php echo $total_payments[0][0]; ?></b></td>
					<td valign="top" align="center" class="tableBody" bgcolor="#DDDDDD"><b><?php echo $total_to_pay; ?></b></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
	<br></br>
<?php
// #### FOOTER SECTION
$smarty->display('footer.tpl');

?>
