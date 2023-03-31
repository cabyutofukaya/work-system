<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Branch;
use App\Models\BusinessDistrict;
use App\Models\ClientTypeRestaurant;
use App\Models\ClientTypeTaxibus;
use App\Models\ClientTypeTravel;
use App\Models\ClientTypeTruck;
use App\Models\ContactPerson;
use App\Models\Genre;
use App\Models\LatestEvaluation;
use App\Models\Product;
use App\Models\ReportContent;
use App\Models\SalesTodo;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Client
 * 
 * @property int $id
 * @property string|null $image
 * @property string $client_type_id
 * @property string $name
 * @property string|null $name_kana
 * @property string|null $postcode
 * @property string|null $prefecture
 * @property string|null $address
 * @property string|null $lat
 * @property string|null $lng
 * @property string|null $url
 * @property string|null $email
 * @property string|null $representative
 * @property string|null $tel
 * @property string|null $fax
 * @property string|null $business_hours
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Branch[] $branches
 * @property Collection|BusinessDistrict[] $business_districts
 * @property Collection|Genre[] $genres
 * @property Collection|Product[] $products
 * @property ClientTypeRestaurant $client_type_restaurant
 * @property ClientTypeTaxibus $client_type_taxibus
 * @property ClientTypeTravel $client_type_travel
 * @property ClientTypeTruck $client_type_truck
 * @property Collection|ContactPerson[] $contact_people
 * @property Collection|LatestEvaluation[] $latest_evaluations
 * @property Collection|ReportContent[] $report_contents
 * @property Collection|SalesTodo[] $sales_todos
 * @property Collection|Vehicle[] $vehicles
 *
 * @package App\Models\Base
 */
class Client extends Model
{
	use SoftDeletes;
	protected $table = 'clients';

	public function branches()
	{
		return $this->hasMany(Branch::class);
	}

	public function business_districts()
	{
		return $this->hasMany(BusinessDistrict::class);
	}

	public function genres()
	{
		return $this->belongsToMany(Genre::class)
					->withPivot('id')
					->withTimestamps();
	}

	public function products()
	{
		return $this->belongsToMany(Product::class)
					->withPivot('id')
					->withTimestamps();
	}

	public function client_type_restaurant()
	{
		return $this->hasOne(ClientTypeRestaurant::class);
	}

	public function client_type_taxibus()
	{
		return $this->hasOne(ClientTypeTaxibus::class);
	}

	public function client_type_travel()
	{
		return $this->hasOne(ClientTypeTravel::class);
	}

	public function client_type_truck()
	{
		return $this->hasOne(ClientTypeTruck::class);
	}

	public function contact_people()
	{
		return $this->hasMany(ContactPerson::class);
	}

	public function latest_evaluations()
	{
		return $this->hasMany(LatestEvaluation::class);
	}

	public function report_contents()
	{
		return $this->hasMany(ReportContent::class);
	}

	public function sales_todos()
	{
		return $this->hasMany(SalesTodo::class);
	}

	public function vehicles()
	{
		return $this->hasMany(Vehicle::class);
	}
}
