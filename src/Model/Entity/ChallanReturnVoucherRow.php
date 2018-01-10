<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChallanReturnVoucherRow Entity
 *
 * @property int $id
 * @property int $challan_return_voucher_id
 * @property int $item_id
 * @property int $quantity
 * @property int $challan_row_id
 *
 * @property \App\Model\Entity\ChallanReturnVoucher $challan_return_voucher
 * @property \App\Model\Entity\Item $item
 * @property \App\Model\Entity\ChallanRow $challan_row
 */
class ChallanReturnVoucherRow extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
