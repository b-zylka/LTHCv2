<?php
/**
*   ConnectWise PHP/SOAP API
*
*   Written by Brandon Zylka, 2015.
*
*		Example Usage:
*			$connectwise = new ConnectWise;
*			$connectwise->setAction("GetCompany");
*			$options = array('id' => '11317'); // ConnectWise RecID
*			$connectwise->setParameters($options);
*			$ret = $connectwise->makeCall();
*
*			foreach($ret as $k=>$v) {
*  			print "CW Company Name: " . $v->CompanyName . "<br>";
*  			print "CW Address: " . $v->DefaultAddress->City . "<br>";
*			}
*
*		Example of setting variables at runtime.
*			$connectwise->useSSL(TRUE);
*			$connectwise->setPassword('bobDole96');
*			$connectwise->setCWHost("connectwise.example.com");
*
*/

class cwAPI {

	/* Define these variables */
	private $use_ssl = TRUE;
	private $cw_host = "connect.example.com"; // Input your ConnectWise URL
	private $co_id = "company"; // Input your ConnectWise company ID
	private $username = "user"; // Input your integrator login ID
	private $password = "password"; // Input your integrator password
	private $creds = array('CompanyId' => 'company', 'IntegratorLoginId' => 'username', 'IntegratorPassword' => 'password');

  /* You shouldn't need to edit anything beyond this point */
	private $base_url_ext = '';
	private $base_url = '';
	private $actionName = '';
  private $lastURL = '';
	private $parms = array();

	public function __construct($init_vars=array()) {
		if(isset($init_vars['cw_host'])) { $this->setCWHost($init_vars['cw_host']); }
		$this->base_url = ($this->use_ssl?"https://":"http://").$this->cw_host;
	}

	public function useSSL($yn_ssl=TRUE) { $this->use_ssl = $yn_ssl; }
	public function getCWUrl() { return $this->base_url; }
	public function setCOID($new_co_id) { $this->co_id = $new_co_id; }
	public function getCOID() { return $this->co_id; }
	public function setUsername($username) { $this->username = $username; }
	public function getUsername() { return $this->username; }
	public function setPassword($password) { $this->password = $password; }
	public function getPassword() { return $this->password; }
	public function setAction($new_action) { $this->actionName = $new_action; }
	public function getAction() { return $this->actionName; }
	public function setParameters($new_parms) { $this->parms = $new_parms; }
	public function getParameters() { return $this->parms; }
	public function setParameterValue($parm_key, $parm_val) { $this->parms[$parm_key] = $parm_val; }
	public function getParameterValue($parm_key) { return $this->parms[$parm_key]; }
	public function setCWHost($newCWHost) { $this->cw_host = $newCWHost; }
	public function getCWHost() { return $this->cw_host; }

	public function makeCall() {
		$this->setBaseUrlExt();
    $this->lastURL=$this->base_url.'/v4_6_release/apis/2.0/'.$this->base_url_ext;
		$soap = new SoapClient($this->lastURL);
		$action = $this->actionName;
		$call = array();
		$call['credentials'] = $this->creds;
		foreach($this->parms as $k=>$v) {
			$call[$k] = $v;
		}
		return $soap->$action($call);
		}

	public function setBaseUrlExt () {
		Switch ($this->actionName) {
      case "AddActivity":
      case "AddOrUpdateActivity":
      case "DeleteActivity":
      case "FindActivities":
      case "GetActivity":
      case "LoadActivity":
      case "UpdateActivity":
				$this->base_url_ext="ActivityApi.asmx?wsdl";
				break;

      case "AddOrUpdateAgreement":
      case "AddOrUpdateAgreementAdjustment":
      case "AddOrUpdateAgreementSite":
      case "AddOrUpdateAgreementWorkRole":
      case "AddOrUpdateAgreementWorkType":
      case "DeleteAgreement":
      case "DeleteAgreementAdjustment":
      case "DeleteAgreementSite":
      case "DeleteAgreementWorkRole":
      case "DeleteAgreementWorkType":
      case "FindAgreementAdjustments":
      case "FindAgreements":
      case "FindAgreementSites":
      case "FindAgreementWorkRoles":
      case "FindAgreementWorkTypes":
      case "GetAgreement":
      case "GetAgreementAdjustment":
      case "GetAgreementSite":
      case "GetAgreementWorkRole":
      case "GetAgreementWorkType":
      case "GetAgreementAddition":
      case "AddOrUpdateAgreementAddition":
      case "FindAgreementAdditions":
      case "DeleteAgreementAddition":
      case "GetAgreementWorkRoleExclusion":
      case "GetAgreementWorkTypeExclusion":
      case "FindAgreementExclusions":
      case "AddOrRemoveAgreementWorkTypeExclusion":
      case "AddOrRemoveAgreementWorkRoleExclusion":
      case "GetAgreementBoardDefault":
      case "FindAgreementBoardDefaults":
      case "AddOrUpdateAgreementBoardDefault":
      case "DeleteAgreementBoardDefault":
				$this->base_url_ext="AgreementApi.asmx?wsdl";
				break;

      case "FindCompanies":
      case "AddCompany":
      case "AddOrUpdateCompany":
      case "AddOrUpdateCompanyNote":
      case "DeleteCompany":
      case "DeleteCompanyNote":
      case "DeletePartnerCompanyNote":
      case "GetAllCompanyNotes":
      case "GetAllPartnerCompanyNotes":
      case "GetCompany":
      case "GetCompanyNote":
      case "GetPartnerCompanyNote":
      case "GetCompanyProfile":
      case "GetPartnerCompanyProfile":
      case "LoadCompany":
      case "SetCompanyDefaultContact":
      case "SetPartnerCompanyDefaultContact":
      case "UpdateCompany":
      case "UpdateCompanyProfile":
      case "UpdatePartnerCompanyProfile":
				$this->base_url_ext="CompanyApi.asmx?wsdl";
				break;

			case "AddConfiguration":
			case "AddConfigurationType":
			case "AddOrUpdateConfiguration":
			case "AddOrUpdateConfigurationType":
			case "DeleteConfiguration":
			case "DeleteConfigurationType":
			case "DeleteConfigurationTypeQuestion":
			case "DeletePossibleResponse":
			case "FindConfigurationCount":
			case "FindConfigurationsCount":
			case "FindConfigurationTypes":
			case "FindConfigurations":
			case "GetConfiguration":
			case "GetConfigurationType":
			case "LoadConfiguration":
			case "LoadConfigurationType":
			case "UpdateConfigration":
			case "UpdateConfigrationType":
				$this->base_url_ext="ConfigurationApi.asmx?wsdl";
				break;

      case "AddContactToGroup":
      case "AddOrUpdateContact":
      case "AddOrUpdateContactCommunicationItem":
      case "AddOrUpdateContactNote":
      case "Authenticate":
      case "DeleteContact":
      case "DeleteContactCommunicationItem":
      case "DeleteNote":
      case "FindContactCount/FindContactsCount":
      case "FindContacts":
      case "GetAllCommunicationTypesAndDescription":
      case "GetAllContactCommunicationItems":
      case "GetAllContactNotes":
      case "GetAvatarImage":
      case "GetContact":
      case "GetContactCommunicationItem":
      case "GetContactNote":
      case "GetPortalConfigSettings":
      case "GetPortalLoginCustomizations":
      case "GetPortalSecurity":
      case "GetPresenceStatus":
      case "LoadContact":
      case "RemoveContactFromGroup":
      case "RequestPassword":
      case "SetDefaultContactCommunicationItem":
      case "UpdatePresenceStatus":
				$this->base_url_ext="ContactApi.asmx?wsdl";
				break;

      case "AddDocuments":
      case "DeleteDocument":
      case "FindDocuments":
      case "GetDocument":
				$this->base_url_ext="DocumentApi.asmx?wsdl";
				break;

      case "AddOrUpdateSpecialInvoice":
      case "AddOrUpdateSpecialInvoiceProduct":
      case "DeleteSpecialInvoice":
      case "DeleteSpecialInvoiceByInvoiceNumber":
      case "DeleteSpecialInvoiceProduct":
      case "FindInvoiceCount":
      case "FindInvoices":
      case "FindSpecialInvoices":
      case "GetApplyToForCompanyByType":
      case "GetInvoice":
      case "GetInvoiceByInvoiceNumber":
      case "GetInvoicePdf":
      case "GetSpecialInvoice":
      case "GetSpecialInvoiceByInvoiceNumber":
      case "LoadInvoice":
				$this->base_url_ext="InvoiceApi.asmx?wsdl";
				break;

      case "GetManagedGroup":
      case "GetManagedServers":
      case "GetManagedWorkstations":
      case "GetManagementItSetupsName":
      case "UpdateManagedDevices":
      case "UpdateManagedServers":
      case "UpdateManagedWorkstations":
      case "UpdateManagementSolution":
      case "UpdateManagementSummaryReports":
      case "UpdateSpamStatsDomains":
				$this->base_url_ext="ManagedDeviceApi.asmx?wsdl";
				break;

      case "RecordCampaignImpression":
      case "RecordEmailOpened":
      case "RecordFormSubmission":
      case "RecordLinkClicked":
				$this->base_url_ext="MarketingApi.asmx?wsdl";
				break;

      case "FindMembers":
      case "AuthenticateSession":
      case "CheckConnectWiseAuthenticationCredentials":
      case "CreateAuthenticatedMemberHashToken":
      case "GetMemberIdByRemoteSupportPackageAuthenticationCredentials":
      case "IsValidMemberIdAndPassword":
				$this->base_url_ext="MemberApi.asmx?wsdl";
				break;

      case "AddForecastAndRecurringRevenue":
      case "AddOpportunity":
      case "AddOpportunityDocuments":
      case "AddOpportunityItem":
      case "AddOrUpdateForecastAndRecurringRevenue":
      case "AddOrUpdateOpportunity":
      case "AddOrUpdateOpportunityItem":
      case "DeleteForecast":
      case "DeleteOpportunity":
      case "DeleteOpportunityDocument":
      case "DeleteOpportunityItem":
      case "DeleteOpportunityNote":
      case "DeleteRecurringRevenue":
      case "FindOpportunities":
      case "FindOpportunityCount":
      case "GetOpportunity":
      case "GetOpportunityDocuments":
      case "LoadOpportunity":
      case "UpdateForecastAndRecurringRevenue":
      case "UpdateOpportunity":
      case "UpdateOpportunityItem":
				$this->base_url_ext="OpportunityApi.asmx?wsdl";
				break;

      case "OpportunityToProjectConversion":
      case "OpportunityToSalesOrderConversion":
      case "OpportunityToTicketConversion":
				$this->base_url_ext="OpportunityConversionApi.asmx?wsdl";
				break;

      case "AddOrUpdateProduct":
      case "AddProduct":
      case "DeleteProduct":
      case "FindProducts":
      case "GetProduct":
      case "GetQuantityOnHand":
      case "LoadProduct":
      case "UpdateProduct":
      case "AddOrUpdateProductPickedandShipped":
      case "GetProductPickedandShipped":
      case "DeleteProductPickedandShipped":
				$this->base_url_ext="ProductApi.asmx?wsdl";
				break;

      case "AddOrUpdateProject":
      case "AddOrUpdateProjectContact":
      case "AddOrUpdateProjectNote":
      case "AddOrUpdateProjectPhase":
      case "AddOrUpdateProjectTeamMember":
      case "AddOrUpdateProjectTicket":
      case "AddOrUpdateProjectWorkPlan":
      case "ConvertServiceTicketToProjectTicket":
      case "DeleteProject":
      case "DeleteProjectContact":
      case "DeleteProjectNote":
      case "DeleteProjectPhase":
      case "DeleteProjectTeamMember":
      case "DeleteProjectTicket":
      case "FindPhases":
      case "FindProjectContacts":
      case "FindProjectCount":
      case "FindProjectNotes":
      case "FindProjectTeamMembers":
      case "FindProjectTickets":
      case "FindProjects":
      case "GetProject":
      case "GetProjectContact":
      case "GetProjectNote":
      case "GetProjectPhase":
      case "GetProjectTeamMember":
      case "GetProjectTicket":
      case "GetProjectWorkPlan":
      case "LoadProjectWorkPlan":
				$this->base_url_ext="ProjectApi.asmx?wsdl";
				break;

      case "AddOrUpdatePurchaseOrder":
      case "AddOrUpdatePurchaseOrderLineItem":
      case "AddPurchaseOrder":
      case "AddPurchaseOrderLineItem":
      case "CreatePurchaseOrderFromProductDemandsAction":
      case "DeletePurchaseOrder":
      case "DeletePurchaseOrderLineItem":
      case "FindPurchaseOrders":
      case "GetAllOpenProductDemands":
      case "GetPurchaseOrder":
      case "LoadPurchaseOrder":
      case "UpdatePurchaseOrder":
      case "UpdatePurchaseOrderLineItem":
				$this->base_url_ext="PurchasingApi.asmx?wsdl";
				break;

      case "GetPortalReports":
      case "GetReportFields":
      case "GetReports":
      case "RunPortalReport":
      case "RunReportCount":
      case "RunReportQuery":
      case "RunReportQueryWithFilters":
      case "RunReportQueryWithTimeout":
        $this->base_url_ext="ReportingApi.asmx?wsdl";
        break;

      case "AddOrUpdateActivityScheduleEntry":
      case "AddOrUpdateMiscScheduleEntry":
      case "AddOrUpdateTicketScheduleEntry":
      case "DeleteActivityScheduleEntry":
      case "DeleteMiscScheduleEntry":
      case "DeleteTicketScheduleEntry":
      case "FindScheduleEntries":
      case "GetActivityScheduleEntry":
      case "GetMiscScheduleEntry":
      case "GetTicketScheduleEntry":
				$this->base_url_ext="SchedulingApi.asmx?wsdl";
				break;

      case "AddOrUpdateServiceTicketViaCompanyIdentifier":
      case "AddOrUpdateServiceTicketViaCompanyId":
      case "AddOrUpdateServiceTicketViaManagedIdentifier":
      case "AddOrUpdateServiceTicketManagedId":
      case "AddOrUpdateTicketNote":
      case "AddOrUpdateTicketProduct":
      case "AddServiceTicketToKnowledgebase":
      case "AddServiceTicketViaCompanyIdentifier":
      case "AddServiceTicketViaManagedIdentifier":
      case "AddTicketDocuments":
      case "AddTicketProduct":
      case "DeleteServiceTicket":
      case "DeleteTicketDocument":
      case "DeleteTicketProduct":
      case "FindServiceTicketCount":
      case "GetTicketCount":
      case "FindServiceTickets":
      case "GetDocument":
      case "GetServiceStatuses":
      case "GetServiceTicket":
      case "GetTicketDocuments":
      case "GetTicketProductList":
      case "LoadServiceTicket":
      case "SearchKnowledgebase":
      case "SearchKnowledgebaseCount":
      case "UpdateServiceTicketViaCompanyIdentifier":
      case "UpdateServiceTicketViaManagedIdentifier":
      case "UpdateTicketProduct":
				$this->base_url_ext="ServiceTicketApi.asmx?wsdl";
				break;

      case "GetConnectWiseVersion":
      case "GetConnectWiseVersionInfo":
      case "IsCloud":
				$this->base_url_ext="SystemApi.asmx?wsdl";
				break;

      case "AddOrUpdateTimeEntry":
      case "AddTimeEntry":
      case "DeleteTimeEntry":
      case "FindTimeEntries":
      case "GetTimeEntry":
      case "LoadTimeEntry":
      case "UpdateTimeEntry":
				$this->base_url_ext="TimeEntryApi.asmx?wsdl";
				break;
		}
	}
}
