<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use YooKassa\Validator\Constraints\Length;
use Illuminate\Support\Str;

class Account extends Model
{
    use HasFactory;
    protected $fillable = [

        'account_type',
        'acc_code',
        'parent_id',
        'balance',
        'acc_code_base',
        'created_by',
        'updated_by',
        'is_deleted',
    ];
    protected $table = "accounts";
    protected $appends = [
        'name'
    ];

    private  const digits_lenght = 7;

    // Accounts Parent && Children For Tree Accounts 🌲
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }



    // Account Transaction : Records every movement (debit or credit) that affects account balances. 💹
    public function transaction()
    {
        $this->hasMany(AccountTransaction::class, 'account_id');
    }

    // Account History : Logs ⚙ all Account changes (audit trail). 🧾
    public function history()
    {
        $this->hasMany(AccountHistory::class, 'account_id');
    }


    // Translations and Multi-Language ⭐
    public function lang()
    {
        $l = app()->getLocale();
        return AccountLang::where('account_id', $this->id)->where('lang', $l)->first();
    }


    public function getNameAttribute()
    {

        return $this->lang()->name ?? Null;
    }

    protected static function booted()
    {
        static::creating(function ($account) {
            // Pre-insertion logic here
            $r = self::generateAccCode($account->parent_id);
            $account->acc_code = $r['account_code'];
            $account->acc_code_base = $r['account_base_code'];
        });
        static::updating(function ($account) {
            // Pre-Updating logic here
            $r = self::generateAccCode($account->parent_id,$account);
            $account->acc_code = $r['account_code'];
            $account->acc_code_base = $r['account_base_code'];
        });
    }

    public function getLevel(): int
    {
        $level = 0;
        $account = $this;

        while ($account->parent) {
            $level++;
            $account = $account->parent;
        }

        return $level;
    }

    public static function generateAccCode(?int $parentId = null ,$account = null)
    {
        //Case1: if the node is root
        //==========================

        $maxAccRootCode = Account::whereNull('parent_id')
            ->orderBy('acc_code', 'desc')
            ->select('acc_code', 'id')
            ->first();
            if ($parentId === null && $maxAccRootCode) {
                //check existing roots, if root is exist, get max
                $maxAccRootInt = ((int)substr($maxAccRootCode->acc_code, 0, 1)) + 1;

            $codeFactor = 1;
                $codeFactor = pow(10, self::digits_lenght - 1);
            $accCode = sprintf('%0' . self::digits_lenght . 'd', $maxAccRootInt * $codeFactor);
            return array('account_code' => $accCode, 'account_base_code' => $maxAccRootInt);

        } elseif ($parentId === null && !$maxAccRootCode) { // IF Wee Dont Have any Root
            $codeFactor = pow(10, self::digits_lenght - 1);
            return array('account_code' => $codeFactor, 'account_base_code' => 1);
        }
        
        // Case2: if the node is not root
        //==============================
        $parent = Account::findOrFail($parentId);
        // Sup Cases Of Case : 2  ✔
        // Case 2-1 :  If  Created and This Parent Have Root and Children
        if($parent->parent && $parent->children->count() <= 0){ // If 
                $root = $parent->parent; // Get Root => Grand Parent 
                $parentOfRoot = $parent; // Get Parent => Parent Children Of Grand Parent

                $baseCodeofRoot = $parent->parent->acc_code_base; // Root Base Code
                $accCodeofRoot = $parent->parent->acc_code; // Root Account Code 
                $baseCodeofParent= $parent->acc_code_base; // Parent Of Root Base Code 
                $accCodeofParent= $parent->acc_code;     //   Paremt of Root Accpunt Code 
            /*
                =========================================================
                | Childed Of Root Updated To Parent => Have New Children |
                ==========================================================
            */
            // Insert Base Code after the first digit
            $positionLevel = $parent->getLevel(); // Get Level Example : Parent 1 , Parent 2 , Parent 3 For One Root 
            $modified = substr($accCodeofRoot , 0, $positionLevel) . $baseCodeofRoot . substr($accCodeofRoot, 1); // Generate Account Code
            $result = intval($modified); // Result
            $parent->acc_code = $result;
            Self::where('id',$parent->id)->update(['acc_code'=>$result]);
            //                                             End                                     
            /*
            ====================================
            | Create Children Of Parent of Root |
            ====================================
            */
            $newChildren = $parent->children->count() + 1;
            $accCode = $result  + $newChildren ;
            //                                              End
            return array('account_code' => $accCode, 'account_base_code' => $baseCodeofParent);  
        }elseif(empty($parent->parent)){ // When Update Account Code And Root => We Need Optimise Code ‼
            $newRoot = $parent; // Get Parent => Parent Children Of Grand Parent
            $accCodeofRoot = $newRoot->acc_code; // Get Parent => Parent Children Of Grand Parent
            $latestParentOfRoot = Account::where('parent_id', $newRoot->id)
            ->orderBy('acc_code','desc')
            ->first();
            $positionLevel = $parent->getLevel()  ; // Get Level Example : Parent 1 , Parent 2 , Parent 3 For One Root 
            $parentsOfRoot = $latestParentOfRoot->children->count() + 1;
            $baseCodeofRoot = $newRoot->acc_code_base; // Get Base Code Of Root 

            $modified = substr($latestParentOfRoot->acc_code, 0, $baseCodeofRoot) . $baseCodeofRoot . substr($latestParentOfRoot->acc_code, 1); // Generate Account Code
            $result = intval($modified)  ; // Result
            $newCodeRoot = Self::where('id', $account->id)->update(['acc_code' => $result]);
           // Updated Childrens
           $newChildren = $account->children->count() + 1;
           $accCode = $result  + $newChildren;
           $parent;
           $data = [];
                $childCount = 1 ; 
            foreach($account->children as $children){
                $childCount ++;
               $newCodeRoot = Self::where('id', $children->id)->update(['acc_code' =>  $result + $childCount]);
            }
            return array('account_code' => $result, 'account_base_code' => $baseCodeofRoot);
        }else{ // Case 2-2 If Children of Root "Parent" Have Children 
            $root = $parent->parent; // Get Root => Grand Parent 
            $parentOfRoot = $parent; // Get Parent => Parent Children Of Grand Parent

            $baseCodeofRoot = $parent->parent?->acc_code_base; // Root Base Code
            $accCodeofRoot = $parent->parent?->acc_code; // Root Account Code 
            $baseCodeofParent = $parent->acc_code_base; // Parent Of Root Base Code 
            $accCodeofParent = $parent->acc_code;     //   Paremt of Root Accpunt Code
            /*
            ====================================
            | Create Children Of Parent of Root |
            ====================================
            */
            $newChildren = $parent->children->count() + 1;
            $accCode = $accCodeofParent  + $newChildren;
            return array('account_code' => $accCode, 'account_base_code' => $baseCodeofParent);
        }
        /*if the parent is root, don't change parent code any case
        node code: suffix*/
        if (is_null($parent->parent_id) && is_null($parent->children) ) { // Notes I will Add specific condation if Parent Has Children
            $childrenCount = Account::where('parent_id', $parentId)->count();
            dd($childrenCount);
            $factor = $childrenCount>0? $childrenCount+1 : 1;
            $parentAccCode = ((int)$parent->acc_code) + $factor;
            $accCode = sprintf('%0' . self::digits_lenght . 'd', $parentAccCode);
            // $length = DB::table('accounts')
            //     ->selectRaw('MAX(CHAR_LENGTH(acc_code)) as max_length')
            //     ->where('parent_id', $parent->parent_id)
            //     ->value('max_length');
            if (strlen($accCode) <= 9) {
                throw new \Exception("Generated account code exceeds maximum allowed length of 9 digits.");
            }
        }elseif(is_null($parent->parent_id)){
            $childrenCount = Account::where('parent_id', $parentId)->count();
            $factor = $childrenCount > 0 ? $childrenCount + 1 : 1;
            $parentAccCode = ((int)$parent->acc_code) + $factor;
            $accCode = sprintf('%0' . self::digits_lenght . 'd', $parentAccCode);
            
            return array('account_code' => $accCode, 'account_base_code' => $factor);
        }
        $childsCount = Account::where('parent_id', $parentId)->count();
        /*if the parent is already parent do not change the code of parent
        get max code if the parent childrens and increment, node code: suffix*/
        if ($childsCount >= 1 && $parent->children->count() >= 1) {
            // dd($parent->acc_code_base);
            $latestchildOfParent = Account::where('parent_id', $parentId)
            ->orderBy('id', 'desc')
            ->first(); // Get Latest Child ;
            $parentAccCode = ((int)$parent->acc_code) + 1 + $latestchildOfParent->acc_code_base; // Add to father and not last child => Duplicated‼️
            $accCode = sprintf('%0' . self::digits_lenght . 'd', $parentAccCode);
            return array('account_code' => $accCode, 'account_base_code' => $latestchildOfParent->acc_code_base + 1);
        }
        /*if the parent is child, convert it to parent, node code: suffix
        node parent: prefix*/
        if ($parent == 0) {
            //change parent from suffix to prefix
            $parentOfParent = Account::findOrFail($parent->parent_id);
            $parentAccCode = $parentOfParent->acc_code;
            $cParent = $parentId;
            while($cParent !== Null){
                $current = Account::findOrFail($cParent);
                $arr[] = $current->acc_code_base;
                $cParent = $current->parent_id;
            }
            $newAccParentCode = implode(array_reverse($arr)) ;
            // dd($newAccParentCode);

            $codeFactor = 1;
            for ($i = 1; $i < self::digits_lenght - strlen($newAccParentCode) + 1; $i++)
                $codeFactor *= 10;
            $newAccParentCode *= $codeFactor;

            /*
            $length = DB::table('accounts')
                ->selectRaw('MAX(CHAR_LENGTH(acc_code_base)) as max_length')
                ->where('parent_id', $parent->parent_id)
                ->value('max_length');
            */
            $updateParent = Account::where('id', $parentId)->update(['acc_code' => $newAccParentCode]);
            $crrAccCode = $newAccParentCode + 1;
            return array('account_code' => $crrAccCode, 'account_base_code' => 1);
        }
    }

    public static function regenerateSubtreeCodes(Account $account, ?int $newParentId)
    {
        // 1. Generate new code for the moved account under new parent
        $newCodeData = self::generateAccCode($newParentId);
        $account->acc_code = $newCodeData['account_code'];
        $account->acc_code_base = $newCodeData['account_base_code'];
        $account->parent_id = $newParentId;
        $account->save();

        // 🚨 Important: get the FRESH account with new acc_code before recursion
        $account = Account::find($account->id);

        // 2. Recalculate and update children recursively
        $children = Account::where('parent_id', $account->id)->get();
        foreach ($children as $child) {
            self::regenerateSubtreeCodes($child, $account->id);
        }
    }



    public static function   generateBaseAccCode(?int $accCode = null)
    {
        $baseCode = Str::of($accCode)->remove('0')->__toString();
        $baseCode = substr($baseCode, -1);
        dd($baseCode);
    }
}
