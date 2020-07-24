<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array(
  0 =>
  array(
    'name' => 'CRM_Financialreports_Form_Report_Contribute_MonthlyTotal',
    'entity' => 'ReportTemplate',
    'params' =>
    array(
      'version' => 3,
      'label' => 'Contributions by Monthly Total',
      'description' => 'Contribute_MonthlyTotal (com.joineryhq.financialreports)',
      'class_name' => 'CRM_Financialreports_Form_Report_Contribute_MonthlyTotal',
      'report_url' => 'com.joineryhq.financialreports/contribute_monthlytotal',
      'component' => 'CiviContribute',
    ),
  ),
);
