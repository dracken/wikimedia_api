<?

/**
 *          Basic interface to the Wikimedia's English Wikipedia API
 * 
 *      @category   
 *      @author     Derek Boerger
 * 
 *      @since      February 1, 2016
 *      @copyright  2016 Derek Boerger
 *      @version    0.1
 * 
 *      @see https://www.mediawiki.org/wiki/API:Main_page
 *      @see https://en.wikipedia.org/w/api.php
 *      @see https://www.ibm.com/developerworks/xml/library/x-phpwikipedia/index.html
 * 
 *      @example https://en.wikipedia.org/w/api.php?action=query&titles=Main%20Page&prop=revisions&rvprop=content&format=jsonfm
 */

class wikimedia_api
{
    /**
     * API URL
     *  
     * Default English Wikipedia API - basis of all other returns
     * 
     * @access protected
     */
    protected $api_url = "https://en.wikipedia.org/w/api.php";
    
    /**
     *  Protected variables for internal use
     * 
     *  @access protected
     */
    protected $api_header = "WikimediaAPIInterface/0.1 (http://drackenslair.com/wikimedia/; dracken@drackenslair.com) BasedOnSuperLib/1.4";
    
    protected $_action;
    protected $_return;
    protected $_title;
    protected $_prop;
    protected $_rvprop;
    protected $_curl_result;
    protected $_return_uid;
    protected $_pages;
    protected $_pageid;
    protected $_page_title;        
    protected $_revisions;
    protected $_latest_revision;
    protected $_revision_id;
    protected $_parent_id;
    
    /**
     *  Public variables
     * 
     *  @access public
     */
    public $debug_array = array();
    public $json_encoded_result;
    public $json_decoded_result;
    public $action_to_set_return = array();
    public $prop_to_set_return = array();
    public $rvprop_to_set_return = array();
    public $error;
    public $content;
    
    /**
     *  Action Options
     * 
     *  Allowed actions within the Wikipedia API - Query is currently only one being utilized, Help is default
     *  Can be used as a drop down 
     * 
     *  @access public
     */ 
    public $action_options = array("abusefiltercheckmatch", 
                                "abusefilterchecksyntax", 
                                "abusefilterevalexpression", 
                                "abusefilterunblockautopromote", 
                                "addstudents", 
                                "antispoof", 
                                "block", 
                                "bouncehandler", 
                                "centralauthtoken", 
                                "centralnoticechoicedata", 
                                "centralnoticequerycampaign", 
                                "checktoken", 
                                "cirrus-config-dump", 
                                "cirrus-mapping-dump", 
                                "cirrus-settings-dump", 
                                "cirrus-suggest", 
                                "clearhasmsg", 
                                "compare", 
                                "createaccount", 
                                "cxconfiguration", 
                                "cxdelete", 
                                "cxpublish", 
                                "cxsave", 
                                "cxsuggestionlist", 
                                "cxtoken", 
                                "delete", 
                                "deleteeducation", 
                                "deleteglobalaccount", 
                                "echomarkread", 
                                "echomarkseen", 
                                "edit", 
                                "editlist", 
                                "editmassmessagelist", 
                                "emailuser", 
                                "enlist", 
                                "expandtemplates", 
                                "fancycaptchareload", 
                                "featuredfeed", 
                                "feedcontributions", 
                                "feedrecentchanges", 
                                "feedwatchlist", 
                                "filerevert", 
                                "flagconfig", 
                                "flow", 
                                "flow-parsoid-utils", 
                                "flowthank", 
                                "globalblock", 
                                "globaluserrights", 
                                "graph", 
                                "help", //Default
                                "imagerotate", 
                                "import", 
                                "jsonconfig", 
                                "languagesearch", 
                                "liststudents", 
                                "login", 
                                "logout", 
                                "managetags", 
                                "massmessage", 
                                "mobileview", 
                                "move", 
                                "opensearch", 
                                "options", 
                                "pagetriageaction", 
                                "pagetriagelist", 
                                "pagetriagestats", 
                                "pagetriagetagging", 
                                "pagetriagetemplate", 
                                "paraminfo", 
                                "parse", 
                                "parsoid-batch", 
                                "patrol", 
                                "protect", 
                                "purge", 
                                "query",
                                "refresheducation", 
                                "review", 
                                "reviewactivity", 
                                "revisiondelete",
                                "rollback", 
                                "rsd", 
                                "scribunto-console", 
                                "setglobalaccountstatus", 
                                "setnotificationtimestamp", 
                                "sitematrix", 
                                "spamblacklist", 
                                "stabilize", 
                                "stashedit", 
                                "strikevote", 
                                "tag", 
                                "templatedata", 
                                "thank", 
                                "titleblacklist", 
                                "tokens", 
                                "transcodereset", 
                                "ulslocalization", 
                                "unblock", 
                                "undelete", 
                                "upload", 
                                "userrights", 
                                "visualeditor", 
                                "visualeditoredit", 
                                "watch", 
                                "wikilove", 
                                "zeroconfig"                                
                                );
    
    /**
     *      Return Options
     * 
     *  The different ways that the API can return values, JSON FM (formatted) is default, JSON, PhP or XML is for processing, FM variants are for debugging
     *  Can be used as a drop down  
     * 
     *  @access public
     */
    public $return_options = array("json",
                                "jsonfm", //Default
                                "none",
                                "php",
                                "phpfm",
                                "rawfm",
                                "xml",
                                "xmlfm"
                                );

    /**
     *      Property options
     * 
     *  The acceptable properties that can be returned for a query
     *  Can be used as a drop down 
     * 
     *  @see https://www.mediawiki.org/wiki/API:Properties
     * 
     *  @access public
     */
    public $prop_options = array("categories",
                                "categoryinfo",
                                "contributors",
                                "deletedrevisions",
                                "duplicatefiles",
                                "extlinks",
                                "fileusage",
                                "imageinfo",
                                "images",
                                "info",
                                "iwlinks",
                                "langlinks",
                                "links",
                                "linkshere",
                                "pageprops",
                                "redirects",
                                "revisions",
                                "stashimageinfo",
                                "templates",
                                "transcludedin"
                                );

    /**
     * 
     *  Which properties to get for each revision (Default: ids|flags|timestamp|comment|user)
     *  Can be used as a drop down 
     * 
     *  @see https://www.mediawiki.org/wiki/API:Revisions
     * 
     *  @access public
     */
    public $revision_options = array(
                                "ids", //Get the revid and, from 1.16 onward, the parentid. 1.11+
                                "flags", //Whether the revision was a minor edit. 1.11+
                                "timestamp", //The date and time the revision was made.
                                "user", //The user who made the revision, as well as userhidden and anon flags.
                                "userid", //User id of revision creator, as well as userhidden and anon flags. 1.17+
                                "size", //The size of the revision text in bytes. 1.11+
                                "sha1", //SHA-1 (base 16) of the revision. 1.19+
                                "contentmodel", //Content model id of the revision. 1.21+
                                "comment", //The edit comment.
                                "parsedcomment", //The edit/log comment in HTML format with wikilinks and section references expanded into hyperlinks 1.16+
                                "content", //The revision content. If set, the maximum limit will be 10 times as low.
                                "tags", //Any tags for this revision, such as those added by AbuseFilter. 1.16+
                                "rvlimit", //The maximum number of revisions to return. Use the string "max" to return all revisions (subject to being broken up as usual, using continue). (enum) No more than 500 (5000 for bots) allowed.
                                "rvexpandtemplates", //Expand templates in rvprop=content output. 1.12+
                                "rvgeneratexml", //Generate XML parse tree for revision content. 1.14+ (deprecated in 1.26)
                                "rvparse", //Parse revision content. For performance reasons if this option is used, rvlimit is enforced to 1. 1.17+
                                "rvsection", //If rvprop=content is set, only retrieve the contents of this section. This is an integer, not a string title. 1.13+
                                "rvdiffto", //Revision ID to diff each revision to. Use "prev", "next" and "cur" for the previous, next and current revision respectively. 1.15+
                                "rvdifftotext", //Text to diff each revision to. Only diffs a limited number of revisions. Overrides diffto. If rvsection is set, only that section will be diffed against this text. 1.16+
                                "rvdifftotextpst", //Perform a pre-save transform on the text before diffing it. Only valid when used with arvdifftotext. 1.27+
                                "rvcontentformat", //Serialization format used for difftotext and expected for output of content. 1.21+
                                "rvstartid", //Revision ID to start listing from. (enum)
                                "rvendid", //Revision ID to stop listing at. (enum)
                                "rvstart", //Timestamp to start listing from. (enum)
                                "rvend", //Timestamp to end listing at. (enum)
                                "rvdir", //Direction to list in. (enum) (Default: older)
                                "older", //List newest revisions first. NOTE: rvstart/rvstartid has to be higher than rvend/rvendid.
                                "newer", //List oldest revisions first. NOTE: rvstart/rvstartid has to be lower than rvend/rvendid.
                                "rvuser", //Only list revisions made by this user. 1.11+
                                "rvexcludeuser", //Do not list revisions made by this user. 1.11+
                                "rvtag", //Only list revisions tagged with this tag. 1.16+
                                "rvtoken", //Gets the rollback token for each revision. Possible values: rollback. 1.12+ (deprecated in 1.24)
                                "rvcontinue", //When more results are available, use this to continue. This can be used, for example, for fetching the text of all revisions of a page (although an XML export might be more efficient). 1.15+
                                );


    /**
     *      Generate UID
     * 
     *  Generates a unique identifier for use in the requestid of the URL, can be used when multiple requests are made at once
     * 
     *  @var int length
     *      The length of the UID to return, defaults to 10
     * 
     *  @return string
     *      Returns a unique id to identify individual requests
     * 
     *  @access private
     */
    private function generate_uid($length = 10) 
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        return "drackenslair_mediawiki_api_".$randomString;
    }
    /**
     *          Wikimedia API Query
     * 
     *  @param string action 
     *      The method of returning, posting, updating, etc. to the wikimedia api
     *  @param string url
     *      The URL of the wikimedia api
     * 
     *  @return string  
     * 
     *  @example    https://en.wikipedia.org/w/api.php?action=query&titles=Main%20Page&prop=revisions&rvprop=content&format=json
     *      The example queries the main page of the English Wikipedia website and returns a JSON object
     * 
     *  @access private
     */                                
    private function wikimedia_query($title) //$action = "query", $prop = "revisions", $rvprop = "content", $return = "jsonfm", $api_url)
    {
        $uid = $this->generate_uid();
        $action = $this->_action;
        $prop = $this->_prop;
        $rvprop = $this->_rvprop;
        $return = $this->_return;
        $api_url = $this->api_url;
        
        $this->title_clean = urlencode(htmlspecialchars($title, ENT_QUOTES));
        
        $url = $api_url."?action=".$action."&titles=".$this->title_clean."&prop=".$prop."&rvprop=".$rvprop."&format=".$return."&requestid=".$uid;
        
        $curl_init = new mycurl($url);
        $curl_setagent = $curl_init->setUserAgent($this->api_header);
        $curl_execute = $curl_init->createCurl($url);
        $curl_result = $curl_init->__tostring();
        
        $this->debug_array["wikimedia query url"] = htmlentities($url, ENT_QUOTES);
        $this->debug_array["curl execute"] = htmlentities(print_r($curl_execute, true), ENT_QUOTES);
        
        if (!$curl_result)
        {
            $this->error = $curl_execute->getHttpStatus();
            return false;
        }
        else
        {
            $this->_curl_result = $curl_result;
            return true;
        }
    }
    
    
    /**
     *      Function to set the type of action, based off of the action_options array
     * 
     *  @param array action_to_set, default is query
     *      A string to set the type of action compared to the public array of action_options
     * 
     *  @return boolean
     *      Returns true if set, false if not a valid action - would be more ideal to pass through an event error handler
     * 
     *  @access public
     */
    public function set_action($action_to_set = array("query"))
    {
        foreach ($action_to_set AS $action_to_set_value)
        {
            if (in_array($action_to_set_value, $this->action_options))
            {
                $this->action_to_set_return[] = $action_to_set_value;
            }
            else 
            {
                $this->action = NULL;
                return false;
            }
        }
        $this->_action = implode("|", $this->action_to_set_return);
            
        return true;
    }
    
    /**
     *      Method to set the type of return, based off of the return_options array 
     * 
     *  @param string return_to_set, default is json
     *      A string to set the type of return to ingest, default is JSON
     * 
     *  @return boolean
     *      Returns true if set, false if not a valid return - would be more ideal to pass through an event error handler
     * 
     *  @access public
     */
    public function set_return($return_to_set = "json")
    {
        if (in_array($return_to_set, $this->return_options))
        {
            $this->_return = $return_to_set;
            
            return true;
        }
        else
        {
            $this->_return = NULL;
            return false;
        }
    }
    
    /**
     *      Method to set the type of properties to view
     * 
     *  @param  array prop_to_set
     *      The properties to set for viewing, defaults to ALL values
     * 
     *  @return boolean
     */
    public function set_prop($prop_to_set = array("all"))
    {
        if (in_array("all", $prop_to_set))
        {
            $prop_to_set_return = $this->prop_options;
        }
        else 
        {
           foreach ($prop_to_set AS $prop_to_set_value)
            { 
                if (in_array($prop_to_set_value, $this->prop_options))
                {
                    $this->prop_to_set_return[] = $prop_to_set_value;
                }
            }
        }
        
        //Check if any settings were provided, else return false
        if (count($this->prop_to_set_return) <= 0) 
        {
            $this->_prop = NULL;
            return false;
        }
                        
        $this->_prop = implode("|", $this->prop_to_set_return);
        
        return true;
    }
    
    /**
     *      Method to set the type of revision to view
     * 
     *  @param array rvprop_to_set
     *      The revision properties to view, defaults to ALL values
     * 
     *  @return boolean
     */
    public function set_rvprop($rvprop_to_set = array("all"))
    {
        if (in_array("all", $rvprop_to_set))
        {
            $this->rvprop_to_set_return = $this->revision_options;
        }
        else
        {
            foreach($rvprop_to_set AS $rvprop_to_set_value)
            {
                if (in_array($rvprop_to_set_value, $this->revision_options))
                {
                    $this->rvprop_to_set_return[] = $rvprop_to_set_value;
                }
            }
        }
        //Check if any settings were provided, else return false
        if (count($this->rvprop_to_set_return) <= 0)
        {
            $this->_rvprop = NULL;
            return false;
        }
        
        $this->_rvprop = implode("|", $this->rvprop_to_set_return);
        
        return true;
    }
    
    public function set_api_url($url)
    {
        $this->api_url = $url;
    }
    
    /**
     *      cURL execute - run after initalizing 
     * 
     *  @param none
     * 
     *  @return string, false on failure
     */
    public function curl_execute()
    {
        $this->debug_array["title"]     = htmlentities($this->_title, ENT_QUOTES);
        $this->debug_array["action"]    = htmlentities($this->_action, ENT_QUOTES);
        $this->debug_array["return"]    = htmlentities($this->_return, ENT_QUOTES);
        $this->debug_array["prop"]      = htmlentities($this->_prop, ENT_QUOTES);
        $this->debug_array["rvprop"]    = htmlentities($this->_rvprop, ENT_QUOTES);
        
        //$this->wikimedia_query($this->_title);//$api_url,$action, $title, $prop, $rvprop, $return);
        
        if (!$this->wikimedia_query($this->_title))
        
        {
            return false;
        }
        else 
        {
            $this->json_encoded_result = $this->_curl_result;
            $this->json_decoded_result = json_decode($this->json_encoded_result, true);
            return true;
        }
    }
    
    /**
     *      Parse Wiki JSON Decoded Return
     * 
     *  Basic parsing of the JSON decoded return from Wikipedia, not a complete parsing or formatting
     * 
     *  @param array wiki_return
     *      The JSON decoded return
     */
    public function parse_decoded_return($json_decoded_value)
    {
        $this->_return_uid      = $json_decoded_value['requestid'];
        $pages                  = array_keys($json_decoded_value['query']['pages']);
        $this->_pages           = $json_decoded_value['query']['pages'][$pages[0]];
        $this->_pageid          = $this->_pages['pageid'];
        $this->_page_title      = $this->_pages['title'];        
        $this->_revisions       = $this->_pages['revisions'];
        $this->_latest_revision = $this->_revisions[0];
        $this->_revision_id     = $this->_latest_revision['revid'];
        $this->_parent_id       = $this->_latest_revision['parentid'];
        $this->content          = $this->_latest_revision['*'];
        
        #$this->debug_array["wiki return"] = $json_decoded_value;
        $this->debug_array["pages"] = $pages;
        $this->debug_array["latest revision"] = $this->_latest_revision;
        $this->debug_array["content"] = $this->content;
    }
    
    /**
     * 
     * 
     * 
     * 
     */
    public function expand_template()
    {
        $this->title_clean = urlencode(htmlspecialchars($title, ENT_QUOTES));
        
        $template_url = $this->url."?action=expandtemplates&text=%7B%7B".$this->title_clean."%7D%7D&format=".$this->_return;
        
        $template_curl = new $this->mycurl($template_url);
        
        $curl_setagent = $curl_init->setUserAgent($this->api_header);
        $curl_execute = $curl_init->createCurl($template_url);
        $curl_result = $curl_init->__tostring();
        
        $this->debug_array["wikimedia expand template url"] = htmlentities($template_url, ENT_QUOTES);
        $this->debug_array["curl expand template execute"] = htmlentities(print_r($curl_execute, true), ENT_QUOTES);
        
        if (!$curl_result)
        {
            $this->error = $curl_execute->getHttpStatus();
            return false;
        }
        else
        {
            $this->_curl_result = $curl_expand_template_result;
            return true;
        }
    }
    
    /**
     *      Default construct to initiate a new query
     * 
     *  @param array action
     *  @param string title
     *  @param array prop
     *  @param array rvprop
     *  @param string return   
     * 
     */
    public function __construct(array $action, $title, array $prop, array $rvprop, $return)
    {
        $this->set_action($action);
        $this->set_return($return);
        $this->set_prop($prop);
        $this->set_rvprop($rvprop);
        
        $this->_title = htmlentities($title);
        
        $this->debug_array["construct set action"]  = $this->_action;
        $this->debug_array["construct set return"]  = $this->_return;
        $this->debug_array["construct set prop"]    = $this->_prop;
        $this->debug_array["construct set rvprop"]  = $this->_rvprop;
    }    
} 

/**
 *  cURL class 
 * 
 * Borrowed from PhP manual as written by artem at zabsoft dot co dot in
 * 
 *  @see http://us2.php.net/manual/en/book.curl.php
 */
 
class mycurl {
     protected $_useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1'; 
     protected $_url; 
     protected $_followlocation; 
     protected $_timeout; 
     protected $_maxRedirects; 
     protected $_cookieFileLocation = './cookie.txt'; 
     protected $_post; 
     protected $_postFields; 
     protected $_referer ="http://www.google.com"; 

     protected $_session; 
     protected $_webpage; 
     protected $_includeHeader; 
     protected $_noBody; 
     protected $_status; 
     protected $_binaryTransfer; 
     public    $authentication = 0; 
     public    $auth_name      = ''; 
     public    $auth_pass      = ''; 

     public function useAuth($use){ 
       $this->authentication = 0; 
       if($use == true) $this->authentication = 1; 
     } 

     public function setName($name){ 
       $this->auth_name = $name; 
     } 
     public function setPass($pass){ 
       $this->auth_pass = $pass; 
     } 

     public function __construct($url,$followlocation = true,$timeOut = 30,$maxRedirecs = 4,$binaryTransfer = false,$includeHeader = false,$noBody = false) 
     { 
         $this->_url = $url; 
         $this->_followlocation = $followlocation; 
         $this->_timeout = $timeOut; 
         $this->_maxRedirects = $maxRedirecs; 
         $this->_noBody = $noBody; 
         $this->_includeHeader = $includeHeader; 
         $this->_binaryTransfer = $binaryTransfer; 

         $this->_cookieFileLocation = dirname(__FILE__).'/cookie.txt'; 

     } 

     public function setReferer($referer){ 
       $this->_referer = $referer; 
     } 

     public function setCookiFileLocation($path) 
     { 
         $this->_cookieFileLocation = $path; 
     } 

     public function setPost ($postFields) 
     { 
        $this->_post = true; 
        $this->_postFields = $postFields; 
     } 

     public function setUserAgent($userAgent) 
     { 
         $this->_useragent = $userAgent; 
     } 

     public function createCurl($url = 'nul') 
     { 
        if($url != 'nul'){ 
          $this->_url = $url; 
        } 

         $s = curl_init(); 

         curl_setopt($s,CURLOPT_URL,$this->_url); 
         curl_setopt($s,CURLOPT_HTTPHEADER,array('Expect:')); 
         curl_setopt($s,CURLOPT_TIMEOUT,$this->_timeout); 
         curl_setopt($s,CURLOPT_MAXREDIRS,$this->_maxRedirects); 
         curl_setopt($s,CURLOPT_RETURNTRANSFER,true); 
         curl_setopt($s,CURLOPT_FOLLOWLOCATION,$this->_followlocation); 
         curl_setopt($s,CURLOPT_COOKIEJAR,$this->_cookieFileLocation); 
         curl_setopt($s,CURLOPT_COOKIEFILE,$this->_cookieFileLocation); 

         if($this->authentication == 1){ 
           curl_setopt($s, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass); 
         } 
         if($this->_post) 
         { 
             curl_setopt($s,CURLOPT_POST,true); 
             curl_setopt($s,CURLOPT_POSTFIELDS,$this->_postFields); 

         } 

         if($this->_includeHeader) 
         { 
               curl_setopt($s,CURLOPT_HEADER,true); 
         } 

         if($this->_noBody) 
         { 
             curl_setopt($s,CURLOPT_NOBODY,true); 
         } 
         /* 
         if($this->_binary) 
         { 
             curl_setopt($s,CURLOPT_BINARYTRANSFER,true); 
         } 
         */ 
         curl_setopt($s,CURLOPT_USERAGENT,$this->_useragent); 
         curl_setopt($s,CURLOPT_REFERER,$this->_referer); 

         $this->_webpage = curl_exec($s); 
         $this->_status = curl_getinfo($s,CURLINFO_HTTP_CODE); 
         curl_close($s); 

     } 

   public function getHttpStatus() 
   { 
       return $this->_status; 
   } 

   public function __tostring(){ 
      return $this->_webpage; 
   } 
}