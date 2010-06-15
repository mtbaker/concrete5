<?

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('attribute/categories/file');
/**
*
* An object that allows a filtered list of files to be returned.
* @package Files
*
*/
class FileList extends DatabaseItemList { 

	protected $attributeFilters = array();
	protected $autoSortColumns = array('fvFilename', 'fvAuthorName','fvTitle', 'fDateAdded', 'fvDateAdded', 'fvSize');
	protected $itemsPerPage = 10;
	protected $attributeClass = 'FileAttributeKey';
	protected $permissionLevel = 'canSearch';
	
	/* magic method for filtering by attributes. */
	public function __call($nm, $a) {
		if (substr($nm, 0, 8) == 'filterBy') {
			$txt = Loader::helper('text');
			$attrib = $txt->uncamelcase(substr($nm, 8));
			if (count($a) == 2) {
				$this->filterByAttribute($attrib, $a[0], $a[1]);
			} else {
				$this->filterByAttribute($attrib, $a[0]);
			}
		}			
	}

	/** 
	 * Filters by type of collection (using the ID field)
	 * @param mixed $ctID
	 */
	public function filterByExtension($ext) {
		$this->filter('fv.fvExtension', $ext, '=');
	}

	/** 
	 * Filters by type of collection (using the ID field)
	 * @param mixed $ctID
	 */
	public function filterByType($type) {
		$this->filter('fv.fvType', $type, '=');
	}
	
	/** 
	 * Filters by "keywords" (which searches everything including filenames, title, tags, users who uploaded the file, tags)
	 */
	public function filterByKeywords($keywords) {
		$db = Loader::db();
		$keywordsExact = $db->quote($keywords);
		$qkeywords = $db->quote('%' . $keywords . '%');
		$keys = FileAttributeKey::getSearchableIndexedList();
		$attribsStr = '';
		foreach ($keys as $ak) {
			$cnt = $ak->getController();			
			$attribsStr.=' OR ' . $cnt->searchKeywords($keywords);
		}
		$this->filter(false, '(fvFilename like ' . $qkeywords . ' or fvTitle like ' . $qkeywords . ' or fvTags like ' . $qkeywords . ' or u.uName = ' . $keywordsExact . $attribsStr . ')');
	}
	
	/** 
	 * Filters by files found in a certain set. If "false" is provided, we display files not found in any set. */
	public function filterBySet($fs) {
		if ($fs != false) {
			$tableAliasName='fsf'.intval($fs->getFileSetID());
			$this->addToQuery("left join FileSetFiles {$tableAliasName} on {$tableAliasName}.fID = f.fID");
			$this->filter("{$tableAliasName}.fsID", $fs->getFileSetID(), '=');
		} else {
			$s1 = FileSet::getMySets();
			$sets = array();
			foreach($s1 as $fs) {
				$sets[] = $fs->getFileSetID();
			}
			if (count($sets) == 0) {
				return false;
			}
			$db = Loader::db();
			$setStr = implode(',', $sets);
			$this->addToQuery("left join FileSetFiles fsfex on fsfex.fID = f.fID");
			$this->filter(false, '(fsfex.fID is null or (select count(fID) from FileSetFiles where fID = fsfex.fID and fsID in (' . $setStr . ')) = 0)');
		}
	}
	
	/** 
	 * Filters the file list by file size (in kilobytes)
	 */
	public function filterBySize($from, $to) {
		$this->filter('fv.fvSize', $from * 1024, '>=');
		$this->filter('fv.fvSize', $to * 1024, '<=');
	}
	
	/** 
	 * Filters by public date
	 * @param string $date
	 */
	public function filterByDateAdded($date, $comparison = '=') {
		$this->filter('f.fDateAdded', $date, $comparison);
	}
	
	public function filterByOriginalPageID($ocID) {
		$this->filter('f.ocID', $ocID);
	}
	
	public function setPermissionLevel($plevel) {
		$this->permissionLevel = $plevel;
	}
	
	/** 
	 * Filters by tag
	 * @param string $tag
	 */
	public function filterByTag($tag='') { 
		$db=Loader::db();  
		$this->filter(false, "( fv.fvTags like ".$db->qstr("%\n".$tag."\n%")."  )");
	}	
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT DISTINCT f.fID, u.uName as fvAuthorName
		FROM Files f INNER JOIN FileVersions fv ON f.fID = fv.fID 
		LEFT JOIN Users u on u.uID = fv.fvAuthorUID
		');
	}
	
	protected function setupFilePermissions() {
		
		$u = new User();
		if ($this->permissionLevel == false || $u->isSuperUser()) {
			return false;
		}
		$vs = FileSetPermissions::getOverriddenSets($this->permissionLevel, FilePermissions::PTYPE_ALL);
		$nvs = FileSetPermissions::getOverriddenSets($this->permissionLevel, FilePermissions::PTYPE_NONE);
		$vsm = FileSetPermissions::getOverriddenSets($this->permissionLevel, FilePermissions::PTYPE_MINE);
		
		// we remove all the items from nonviewableSets that appear in viewableSets because viewing trumps non-viewing
		
		for ($i = 0; $i < count($nvs); $i++) {
			if (in_array($nvs[$i], $vs)) {
				unset($nvs[$i]);
			}
		}

		// we have $nvs, which is an array of sets of files that we CANNOT see
		// first, we add -1 so that we are always dealing with an array that at least has one value, just for
		// query writing sanity sake
		$nvs[] = -1;
		$vs[] = -1;
		$vsm[] = -1;

		//$this->debug();
		
		// this excludes all file that are found in sets that I can't find
		$this->filter(false, '((select count(fID) from FileSetFiles where FileSetFiles.fID = f.fID and fsID in (' . implode(',',$nvs) . ')) = 0)');		

		$uID = ($u->isRegistered()) ? $u->getUserID() : 0;
		
		// This excludes all files found in sets where I may only read mine, and I did not upload the file
		$this->filter(false, '(f.uID = ' . $uID . ' or (select count(fID) from FileSetFiles where FileSetFiles.fID = f.fID and fsID in (' . implode(',',$vsm) . ')) = 0)');		
		
		$fp = FilePermissions::getGlobal();
		if ($fp->getFileSearchLevel() == FilePermissions::PTYPE_MINE) {
			// this means that we're only allowed to read files we've uploaded (unless, of course, those files are in previously covered sets)
			$this->filter(false, '(f.uID = ' . $uID . ' or (select count(fID) from FileSetFiles where FileSetFiles.fID = f.fID and fsID in (' . implode(',',$vs) . ')) > 0)');		
		}

		// now we filter out files we directly don't have access to
		$groups = $u->getUserGroups();
		$groupIDs = array();
		foreach($groups as $key => $value) {
			$groupIDs[] = $key;
		}
		
		$uID = -1;
		if ($u->isRegistered()) {
			$uID = $u->getUserID();
		}
		
		if (PERMISSIONS_MODEL != 'simple') {
			// There is a really stupid MySQL bug that, if the subquery returns null, the entire query is nullified
			// So I have to do this query OUTSIDE of MySQL and give it to mysql
			$db = Loader::db();
			$fIDs = $db->GetCol("select Files.fID from Files inner join FilePermissions on FilePermissions.fID = Files.fID where fOverrideSetPermissions = 1 and (FilePermissions.gID in (" . implode(',', $groupIDs) . ") or FilePermissions.uID = {$uID}) having max(" . $this->permissionLevel. ") = 0");
			if (count($fIDs) > 0) {
				$this->filter(false, "(f.fID not in (" . implode(',', $fIDs) . "))");
			}			
		}
	}
	
	/** 
	 * Returns an array of page objects based on current settings
	 */
	public function get($itemsToGet = 0, $offset = 0) {
		$files = array();
		Loader::model('file');
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);
		foreach($r as $row) {
			$f = File::getByID($row['fID']);			
			$files[] = $f;
		}
		return $files;
	}
	
	public function getTotal(){
		$files = array();
		Loader::model('file');
		$this->createQuery();
		return parent::getTotal();
	}
	
	//this was added because calling both getTotal() and get() was duplicating some of the query components
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->filter('fvIsApproved', 1);
			$this->setupAttributeFilters("left join FileSearchIndexAttributes on (fv.fID = FileSearchIndexAttributes.fID)");
			$this->setupFilePermissions();
			$this->queryCreated=1;
		}
	}
	
	//$key can be handle or fak id
	public function sortByAttributeKey($key,$order='asc') {
		$this->sortBy($key, $order); // this is handled natively now
	}
	
	public function sortByFileSetDisplayOrder() {
		$this->sortByMultiple('fsDisplayOrder asc', 'fID asc');
	}
	
	public static function getExtensionList() {
		$db = Loader::db();
		$col = $db->GetCol('select distinct(trim(fvExtension)) as extension from FileVersions where fvIsApproved = 1 and fvExtension <> ""');
		return $col;
	}

	public static function getTypeList() {
		$db = Loader::db();
		$col = $db->GetCol('select distinct(trim(fvType)) as type from FileVersions where fvIsApproved = 1 and fvType <> 0');
		return $col;
	}

}

class FileManagerDefaultColumnSet extends DatabaseItemListColumnSet {
	
	public function __construct() {
		$ak1 = FileAttributeKey::getByHandle('width');
		$this->addColumn(new DatabaseItemListColumn('fvType', t('Type'), false));
		$title = new DatabaseItemListColumn('fvTitle', t('Title'));
		$this->addColumn($title);
		$this->addColumn(new DatabaseItemListColumn('fDateAdded', t('Added')));
		$this->addColumn(new DatabaseItemListAttributeKeyColumn($ak1));
		$this->addColumn(new DatabaseItemListColumn('fvDateActive', t('Active')));
		$this->addColumn(new DatabaseItemListColumn('fvSize', t('Size')));
		$this->setDefaultSortColumn($title);
	}
}

class FileManagerAvailableColumnSet extends DatabaseItemListColumnSet {

	public function __construct() {
		$this->addColumn(new DatabaseItemListColumn('fvType', t('Type'), false));
		$this->addColumn(new DatabaseItemListColumn('fvTitle', t('Title')));
		$this->addColumn(new DatabaseItemListColumn('fvAuthorName', t('Author')));
		$this->addColumn(new DatabaseItemListColumn('fDateAdded', t('Added')));
		$this->addColumn(new DatabaseItemListColumn('fvDateActive', t('Active')));
		$this->addColumn(new DatabaseItemListColumn('fvSize', t('Size')));
	}

}