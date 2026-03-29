<?php

namespace App\Repositories\Eloquent;

use App\Models\Campaign;
use App\Repositories\Interfaces\CampaignRepositoryInterface;

class CampaignRepository implements CampaignRepositoryInterface
{

  /*
  * Create new campaign
  * @param array $data
  * @return Campaign
  */
  public function create(array $data)
  {
    return Campaign::create($data);
  }

  /*
  * Update campaign by ID
  * @param array $data
  * @param int $id
  * @return Campaign
  */
  public function update(array $data, $id)
  {
    return Campaign::where('id', $id)->update($data);
  }

  /*
  * Delete campaign by ID
  * @param int $id
  * @return bool
  */
  public function delete($id)
  {
    return Campaign::where('id', $id)->delete();
  }

  /*
  * Get campaign by ID
  * @param int $id
  * @return Campaign
  */
  public function getById($id)
  {
    return Campaign::find($id);
  }

  /*

  * Get all campaigns
  * @return Collection
  */
  public function getAll()
  {
    return Campaign::all();
  }

  /*
  * Get campaigns by user ID
  * @param int $userId
  * @return Collection
  */
  public function getByUserId($userId)
  {
    return Campaign::where('user_id', $userId)->get();
  }

  /*
  * Get campaigns by user ID with pagination
  * @param int $userId
  * @param int $perPage
  * @param int $page
  * @return Collection
  */
  public function getByUserIdWithPagination($userId, $perPage = 10, $page = 1)
  {
    return Campaign::where('user_id', $userId)->paginate($perPage, ['*'], 'page', $page);
  }

  /*
  * Get campaigns that are scheduled and due to send (send_at <= now)
  * @return Collection
  */
  public function getScheduledDue()
  {
    return Campaign::where('status', 'scheduled')
      ->where('send_at', '<=', now())
      ->get();
  }
}
