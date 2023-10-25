<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    use HasFactory, Compoships;

    /**
     * @var string
     */
    protected $connection = 'MAX';

    /**
     * @var string
     */
    protected $table = 'Transaction_History';

    /**
     * @var string[]
     */
    protected $fillable = [
        'PRTNUM_15', 'TNXDTE_15', 'FILL01_15', 'TNXTIM_15', 'ORDNUM_15', 'OPRSEQ_15', 'USRNAM_15', 'OPRID_15',
        'VENID_15', 'WRKCTR_15', 'TNXCDE_15', 'COST_15', 'REASON_15', 'REFDES_15', 'STKID_15', 'TNXQTY_15',
        'REVLEV_15', 'GLREF_15', 'DELTA_15', 'RUNACT_15', 'SHIFT_15', 'POSTED_15', 'LOC_15', 'RUNTTL_15', 'AVECST_15',
        'REV_15', 'REVDTE_15', 'DEFECT_15', 'MCOMP_15', 'MSITE_15', 'UDFKEY_15', 'UDFREF_15', 'SERVICEID_15', 'STKID2_15',
        'XDFINT_15', 'XDFFLT_15', 'XDFBOL_15', 'XDFDTE_15', 'XDFTXT_15', 'FILLER_15', 'MAXID', 'CreatedBy', 'CreationDate',
        'ModifiedBy', 'ModificationDate', 'PMFOH_15', 'CLASS_15', 'SETACT_15', 'PMCOST_15', 'PMMATL_15', 'PMLABOR_15',
        'PMVOH_15', 'PMYIELD_15', 'PMCSTCNV_15', 'PMSUBCST_15', 'PMMATLXY_15', 'PMVOHAMT_15', 'REVALMATL_15', 'REVALFOHAMT_15',
        'REVALLABOR_15', 'REVALVOHAMT_15', 'REVALSUBCONTRACT_15', 'REVALYIELDAMT_15', 'REPID_15',
    ];
}
