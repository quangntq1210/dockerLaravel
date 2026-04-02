<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\CampaignRecipientsRepositoryInterface;
use App\Models\CampaignRecipient;

class CampaignRecipientsRepository implements CampaignRecipientsRepositoryInterface
{

  public function create(array $data)
  {
    return CampaignRecipient::create($data);
  }

  public function createBulk(array $data)
  {
    return CampaignRecipient::insert($data);
  }

  public function update(array $data, $id)
  {
    return CampaignRecipient::where('id', $id)->update($data);
  }

  public function delete($id)
  {
    return CampaignRecipient::where('id', $id)->delete();
  }

  public function deleteByCampaignId($campaignId)
  {
    return CampaignRecipient::where('campaign_id', $campaignId)->delete();
  }


  public function getById($id)
  {
    return CampaignRecipient::find($id);
  }

  
  public function getAll()
  {
    return CampaignRecipient::all();
  }


  public function getByUserId($userId)
  {
    return CampaignRecipient::where('user_id', $userId)->get();
  }


  public function getByUserIdWithPagination($userId, $perPage = 10, $page = 1)
  {
    return CampaignRecipient::where('user_id', $userId)->paginate($perPage, ['*'], 'page', $page);
  }


  public function getPendingByCampaignId($campaignId)
  {
    return CampaignRecipient::where('campaign_id', $campaignId)
      ->where('status', 'pending')
      ->get();
  }


  public function hasPending($campaignId)
  {
    return CampaignRecipient::where('campaign_id', $campaignId)
      ->where('status', 'pending')
      ->exists();
  }
}
