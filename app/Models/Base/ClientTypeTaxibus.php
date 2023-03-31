<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ClientTypeTaxibus
 * 
 * @property int $id
 * @property int $client_id
 * @property int|null $membership_fee
 * @property int|null $fee_taxi_cab
 * @property int|null $fee_taxi_tabinoashi
 * @property int|null $fee_bus_cab
 * @property int|null $fee_bus_tabinoashi
 * @property string|null $category
 * @property bool|null $has_dr_sightseeing
 * @property bool|null $has_dr_female
 * @property bool|null $has_dr_language_english
 * @property bool|null $has_dr_language_chinese
 * @property bool|null $has_dr_language_korean
 * @property bool|null $has_dr_language_other
 * @property bool|null $has_wheelchair
 * @property bool|null $has_baby_seat
 * @property bool|null $has_child_seat
 * @property int|null $fee_child_seat
 * @property bool|null $has_junior_seat
 * @property int|null $fee_junior_seat
 * @property bool|null $is_bus_association_member
 * @property bool|null $has_safety_mark
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Client $client
 *
 * @package App\Models\Base
 */
class ClientTypeTaxibus extends Model
{
	protected $table = 'client_type_taxibus';

	protected $casts = [
		'client_id' => 'int',
		'membership_fee' => 'int',
		'fee_taxi_cab' => 'int',
		'fee_taxi_tabinoashi' => 'int',
		'fee_bus_cab' => 'int',
		'fee_bus_tabinoashi' => 'int',
		'has_dr_sightseeing' => 'bool',
		'has_dr_female' => 'bool',
		'has_dr_language_english' => 'bool',
		'has_dr_language_chinese' => 'bool',
		'has_dr_language_korean' => 'bool',
		'has_dr_language_other' => 'bool',
		'has_wheelchair' => 'bool',
		'has_baby_seat' => 'bool',
		'has_child_seat' => 'bool',
		'fee_child_seat' => 'int',
		'has_junior_seat' => 'bool',
		'fee_junior_seat' => 'int',
		'is_bus_association_member' => 'bool',
		'has_safety_mark' => 'bool'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}
}
