<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2016                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2016
 */
class CRM_Financialreports_Form_Report_Contribute_MonthlyTotal extends CRM_Report_Form {
  /**
   * By default most reports hide contact id.
   * Setting this to true makes it available
   */
  protected $_exposeContactID = TRUE;

  /**
   * List of civicrm entities whose custom fields will be added as columns and
   * filters.
   *
   * @var Array
   */
  protected $_customGroupExtends = array('Contribution');

  /**
   * Whether or not the report is in debug mode. In debug mode, temporary tables
   * are retained after running the report, and debug messages may be displayed
   * using Drupal's dsm() function (if it's available).
   *
   * @var Boolean
   */
  private $_debug = FALSE;

  /**
   * Name of temporary table for summary data.
   *
   * @var String.
   */
  private $_tempTableName = 'temp_monthlytotal';
  
  /**
   * Class constructor.
   */
  public function __construct() {
    $this->_columns = array(
      'civicrm_contact' => array(
        'dao' => 'CRM_Contact_DAO_Contact',
        'fields' => array(
          // See parent::getBasicContactFields() for more fields.
          'sort_name' => array(
            'title' => ts('Contact Name (sortable)'),
            'default' => TRUE,
          ),
          'display_name' => array(
            'title' => ts('Contact Name (display)'),
          ),
          // 'id' is required because we use it in alterDisplay().
          'id' => array(
            'no_display' => TRUE,
            'required' => TRUE,
          ),
          'first_name' => array(
            'title' => ts('First Name'),
          ),
          'nick_name' => array(
            'title' => ts('Nick Name'),
          ),
          'last_name' => array(
            'title' => ts('Last Name'),
          ),
          'gender_id' => array(
            'title' => ts('Gender'),
          ),
          'external_identifier' => array(
            'title' => ts('External identifier'),
          ),
        ),
        'grouping' => 'contact-fields',
      ),
      'civicrm_contribution' => array(
        'dao' => 'CRM_Contribute_DAO_Contribution',
        'grouping' => 'contri-fields',
        'filters' => array(
          'receive_date' => array('operatorType' => CRM_Report_Form::OP_DATE),
          'contribution_status_id' => array(
            'title' => ts('Contribution Status'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::contributionStatus(),
            'default' => array(1),
            'type' => CRM_Utils_Type::T_INT,
          ),
          'currency' => array(
            'title' => ts('Currency'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Core_OptionGroup::values('currencies_enabled'),
            'default' => NULL,
            'type' => CRM_Utils_Type::T_STRING,
          ),
          'financial_type_id' => array(
            'title' => ts('Financial Type'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::financialType(),
            'type' => CRM_Utils_Type::T_INT,
          ),
          'contribution_page_id' => array(
            'title' => ts('Contribution Page'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::contributionPage(),
            'type' => CRM_Utils_Type::T_INT,
          ),
          'total_amount' => array(
            'title' => ts('Contribution Amount'),
          ),
          'non_deductible_amount' => array(
            'title' => ts('Non-deductible Amount'),
          ),
        ),
      ),
      $this->_tempTableName => array(
        'fields' => array(
          '1' => array (
            'title' => ts('January'),
            'grouping' => 'months',
            'default' => TRUE,
          ),
          '2' => array (
            'title' => ts('February'),
            'grouping' => 'months',
            'default' => TRUE,
          ),
          '3' => array (
            'title' => ts('March'),
            'grouping' => 'months',
            'default' => TRUE,
          ),
          '4' => array (
            'title' => ts('April'),
            'grouping' => 'months',
            'default' => TRUE,
          ),
          '5' => array (
            'title' => ts('May'),
            'grouping' => 'months',
            'default' => TRUE,
          ),
          '6' => array (
            'title' => ts('June'),
            'grouping' => 'months',
            'default' => TRUE,
          ),
          '7' => array (
            'title' => ts('July'),
            'grouping' => 'months',
            'default' => TRUE,
          ),
          '8' => array (
            'title' => ts('August'),
            'grouping' => 'months',
            'default' => TRUE,
          ),
          '9' => array (
            'title' => ts('September'),
            'grouping' => 'months',
            'default' => TRUE,
          ),
          '10' => array (
            'title' => ts('October'),
            'grouping' => 'months',
            'default' => TRUE,
          ),
          '11' => array (
            'title' => ts('November'),
            'grouping' => 'months',
            'default' => TRUE,
          ),
          '12' => array (
            'title' => ts('December'),
            'grouping' => 'months',
            'default' => TRUE,
          ),
          'display_total' => array (
            'title' => ts('All Displayed Months'),
            'grouping' => 'months',
            'default' => TRUE,
          ),
          'all_total' => array (
            'title' => ts('All Months'),
            'grouping' => 'months',
          ),
        ),
      ),
    );

    // Check if CiviCampaign is a) enabled and b) has active campaigns
    $config = CRM_Core_Config::singleton();
    $campaignEnabled = in_array("CiviCampaign", $config->enableComponents);
    if ($campaignEnabled) {
      $getCampaigns = CRM_Campaign_BAO_Campaign::getPermissionedCampaigns(NULL, NULL, TRUE, FALSE, TRUE);
      $this->activeCampaigns = $getCampaigns['campaigns'];
      asort($this->activeCampaigns);
    }

    // If we have a campaign, build out the relevant elements
    if ($campaignEnabled && !empty($this->activeCampaigns)) {
      $this->_columns['civicrm_contribution']['filters']['campaign_id'] = array(
        'title' => ts('Campaign'),
        'type' => CRM_Utils_Type::T_INT,
        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
        'options' => $this->activeCampaigns,
      );
    }

    // Add support for tag and group filters.
    $this->_tagFilter = TRUE;
    $this->_groupFilter = TRUE;

    parent::__construct();
  }

  /**
   * Overrides parent::beginPostProcessCommon().  This allows us to build and
   * populate the summary data table before the report runs its own queries.
   */
  function beginPostProcessCommon() {
    parent::beginPostProcessCommon();

    // Build and populate the summary table for this report.
    $this->_buildAggregateTable();
  }
  
  /**
   * Depending on the value of $this->_debug, either indicate that the given
   * table should be temporary, or that it should be created as a regular table
   * for later review. For regular tables, drop the table in case it exists
   * already.
   *
   * @param String $table_name
   * @return string Either "TEMPORARY" or "". This string can be used in a CREATE
   *    TABLE query to create either a temporary or a durable table.
   *
   */
  function _debug_temp_table($table_name) {
    if ($this->_debug) {
      $query = "DROP TABLE IF EXISTS {$table_name}";
      CRM_Core_DAO::executeQuery($query);
      $temporary = '';
    }
    else {
      $temporary = 'TEMPORARY';
    }
    return $temporary;
  }

  /**
   * Debug logger. If $this->_debug is TRUE, send $var to dsm() with label $label.
   */
  function _debugDsm($var, $label = NULL) {
    if ($this->_debug && function_exists('dsm')) {
      dsm($var, $label);
    }
  }

  /**
   * Build and populate the summary table for this report.
   */
  function _buildAggregateTable() {
    $temporary = $this->_debug_temp_table($this->_tempTableName);
    $query = "
      CREATE $temporary TABLE `{$this->_tempTableName}` (
        `contact_id` int(11) NOT NULL,
        `1` decimal(20,2) NOT NULL DEFAULT 0,
        `2` decimal(20,2) NOT NULL DEFAULT 0,
        `3` decimal(20,2) NOT NULL DEFAULT 0,
        `4` decimal(20,2) NOT NULL DEFAULT 0,
        `5` decimal(20,2) NOT NULL DEFAULT 0,
        `6` decimal(20,2) NOT NULL DEFAULT 0,
        `7` decimal(20,2) NOT NULL DEFAULT 0,
        `8` decimal(20,2) NOT NULL DEFAULT 0,
        `9` decimal(20,2) NOT NULL DEFAULT 0,
        `10` decimal(20,2) NOT NULL DEFAULT 0,
        `11` decimal(20,2) NOT NULL DEFAULT 0,
        `12` decimal(20,2) NOT NULL DEFAULT 0,
        `all_total` decimal(20,2) NOT NULL DEFAULT 0,
        `display_total` decimal(20,2) NOT NULL DEFAULT 0,
        PRIMARY KEY (`contact_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    ";
    CRM_Core_DAO::executeQuery($query);

    // Populate from and where clauses based on selected parameters.
    $from = $this->_getAggregateFrom();
    $this->where();

    // Write monthly data into summary table.
    foreach ($this->_columns[$this->_tempTableName]['fields']as $field_key => $field_value) {
      if (intval($field_key) && !empty($this->_params['fields'][$field_key])) {
        $query = "
          INSERT INTO {$this->_tempTableName} (contact_id, `$field_key`)
          SELECT
            {$this->_aliases['civicrm_contact']}.id, @sumtotal := SUM({$this->_aliases['civicrm_contribution']}.total_amount)
          {$from}
          {$this->_where}
            AND month({$this->_aliases['civicrm_contribution']}.receive_date) = '$field_key'
          GROUP BY
            {$this->_aliases['civicrm_contact']}.id
          ON DUPLICATE KEY UPDATE `$field_key` = @sumtotal
        ";
        $this->_debugDsm($query, "Insert query for $field_key");
        CRM_Core_DAO::executeQuery($query);
      }
    }

    // Write "All months" sums into summary table.
    if (!empty($this->_params['fields']['all_total'])) {
      $query = "
        INSERT INTO {$this->_tempTableName} (contact_id, `all_total`)
        SELECT
          {$this->_aliases['civicrm_contact']}.id, @sumtotal := SUM({$this->_aliases['civicrm_contribution']}.total_amount)
        {$from}
        {$this->_where}
        GROUP BY
          {$this->_aliases['civicrm_contact']}.id
        ON DUPLICATE KEY UPDATE `all_total` = @sumtotal
      ";
      $this->_debugDsm($query, "Insert query for $field_key");
      CRM_Core_DAO::executeQuery($query);
    }

    // Write "display total" sums into summary table.
    if (!empty($this->_params['fields']['display_total'])) {
      $query = "
        UPDATE {$this->_tempTableName}
        SET display_total = `1` + `2` + `3` + `4` + `5` + `6` + `7` + `8` + `9` + `10` + `11` + `12`
      ";
      $this->_debugDsm($query, "Insert query for $field_key");
      CRM_Core_DAO::executeQuery($query);
    }

    // Reset where-related values to their defaults, so that future invocations
    // of $this->where() don't use our stale values.
    $this->_where = '';
    $this->_whereClauses = array();
    $this->_havingClauses = array();
  }

  /**
   * Overrides parent::buildQuery().
   */
  function buildQuery($applyLimit = TRUE) {
    // Temporarily remove all filter params so they don't apply to the $where
    // clause in parent::buildQuery(). We've already applied filters in building
    // the temp table, so now we just want all the rows in $this->_tempTableName;
    // applying the filters again at this point will cause SQL errors.  
    $backup_params = $this->_params;
    foreach ($this->_columns as $table_name => $table) {
      if (array_key_exists('filters', $table) && is_array($table['filters'])) {
        foreach ($table['filters'] as $filter_name => $filter) {
          unset($this->_params[$filter_name . '_value']);
          unset($this->_params[$filter_name . '_max']);
          unset($this->_params[$filter_name . '_min']);
          unset($this->_params[$filter_name . '_relative']);
          unset($this->_params[$filter_name . '_from']);
          unset($this->_params[$filter_name . '_to']);
        }
      }
    }
    // Build the query.
    $sql = parent::buildQuery($applyLimit);
    // Return params to their original values; don't know who else will be using them.
    $this->_params = $backup_params;

    return $sql;
  }


  /**
   * Get an SQL FROM clause to populate sums in the summary table, as appropriate
   * for the selected report parameters.
   *
   */
  public function _getAggregateFrom() {
    $from = 'FROM ';
    $from .= "
      civicrm_contact {$this->_aliases['civicrm_contact']}
      INNER JOIN civicrm_contribution   {$this->_aliases['civicrm_contribution']}
        ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id AND
          {$this->_aliases['civicrm_contribution']}.is_test = 0
    ";
    if ($this->isTableSelected('civicrm_financial_type')) {
      $from .= "
        LEFT JOIN civicrm_financial_type  {$this->_aliases['civicrm_financial_type']}
          ON {$this->_aliases['civicrm_contribution']}.financial_type_id ={$this->_aliases['civicrm_financial_type']}.id
      ";
    }
    
    return $from;
  }

  /**
   * Set from clause for the final report output.
   *
   */
  public function from() {
    $this->setFromBase('civicrm_contact');
    $this->_from .= "
        INNER JOIN {$this->_tempTableName} AS {$this->_aliases[$this->_tempTableName]}
          ON {$this->_aliases[$this->_tempTableName]}.contact_id = {$this->_aliases['civicrm_contact']}.id";
  }


  /**
   * Alter display of rows.
   *
   * Iterate through the rows retrieved via SQL and make changes for display purposes,
   * such as rendering contacts as links.
   *
   * @param array $rows
   *   Rows generated by SQL, with an array for each row.
   */
  public function alterDisplay(&$rows) {
    $entryFound = FALSE;
    $contributionStatus = CRM_Contribute_PseudoConstant::contributionStatus();

    foreach ($rows as $rowNum => $row) {

      // convert display_name and sort_name to links
      if (array_key_exists('civicrm_contact_sort_name', $row) &&
        array_key_exists('civicrm_contact_id', $row)
      ) {
        $url = CRM_Utils_System::url('civicrm/contact/view',
          'reset=1&selectedChild=contribute&cid=' . $row['civicrm_contact_id']
        );
        $rows[$rowNum]['civicrm_contact_display_name_link'] = $url;
        $rows[$rowNum]['civicrm_contact_display_name_hover'] = ts("Lists detailed contribution(s) for this record.");
        $rows[$rowNum]['civicrm_contact_sort_name_link'] = $url;
        $rows[$rowNum]['civicrm_contact_sort_name_hover'] = ts("Lists detailed contribution(s) for this record.");
        $entryFound = TRUE;
      }

      // skip looking further in rows, if first row itself doesn't
      // have the column we need
      if (!$entryFound) {
        break;
      }
    }
  }

  /**
   * Build report statistics.
   *
   * Override this method to build your own statistics.
   *
   * @param array $rows
   *
   * @return array
   */
  public function statistics(&$rows) {
    $stats = parent::statistics($rows);
    $stats['counts'] += $this->_statisticsColumnTotals();
    return $stats;
  }

  /**
   * Get summary statistics for each 'sum' column.
   *
   * @return Array Values suitable for merging into the $statistics['counts']
   *    arrey which is returned by this::statistics().
   */
  public function _statisticsColumnTotals() {
    $statistics = array();
    $select = $field_keys = array();
    foreach ($this->_columns[$this->_tempTableName]['fields']as $field_key => $field_value) {
      if (!empty($this->_params['fields'][$field_key])) {
        $select[] = "SUM(`$field_key`) AS `$field_key`";
        $field_keys[] = $field_key;
      }
    }

    $query = "
      SELECT ". implode(',', $select)
      . "FROM {$this->_tempTableName}
    ";
    $dao = CRM_Core_DAO::executeQuery($query);
    $dao->fetch();
    foreach ($field_keys as $field_key) {
      $ts_params = array(
        '1' => $this->_columns[$this->_tempTableName]['fields'][$field_key]['title'],
      );
      $statistics["sum_". $field_key] = array(
        'title' => ts('Column total: %1', $ts_params),
        'value' => $dao->$field_key,
        'type' => CRM_Utils_Type::T_MONEY,
      );
    }
    return $statistics;
  }
}
