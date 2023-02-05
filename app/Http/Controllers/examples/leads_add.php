<?php
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Collections\NullTagsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\BirthdayCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\DateTimeCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\NullCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use App\Models\Lead;
use Carbon\Carbon;
use League\OAuth2\Client\Token\AccessTokenInterface;
use AmoCRM\Collections\UsersCollection;

include_once __DIR__ . '/bootstrap.php';

$accessToken = getToken();

$apiClient->setAccessToken($accessToken)
    ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
    ->onAccessTokenRefresh(
        function (AccessTokenInterface $accessToken, string $baseDomain) {
            saveToken(
                [
                    'accessToken' => $accessToken->getToken(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'baseDomain' => $baseDomain,
                ]
            );
        }
    );




$leadsService = $apiClient->leads();


//Получим сделки
try {

    $leadsCollection = $leadsService->get();

    foreach ($leadsCollection as $lead){

        //получим идентификатор и имя автора сделок
        $userName = "";
        $usersService = $apiClient->users();
        $usersCollection = $usersService->get();
        foreach ($usersCollection as $user){
            if($user->getId() == $lead->getResponsibleUserId()){
                $userName = $user->getName();
            }
        }

        //получаем воронку
        $pipelinesService = $apiClient->pipelines();
        $pipelinesCollection = $pipelinesService->get();
        $pipelineId = 0;
        $pipelineName = " ";
        $statusId = 0;
        $statusName = " ";
        if($pipelinesCollection != null && $lead->getPipeLineId() != null){
            foreach($pipelinesCollection as $pipeline){
                if($pipeline->getId() == $lead->getPipeLineId()){
                    //получаем идентивикатор воронки
                    $pipelineId = $pipeline->getId();
                    $pipelineName = $pipeline->getName();

                    //получаем статус воронки
                    $statusesService = $apiClient->statuses($pipeline->getId());
                    $statusesCollection = $statusesService->get();
                    foreach($statusesCollection as $status){
                        if($status->getId() == $lead->getStatusId()){
                            $statusId = ($status->getId());
                            $statusName = ($status->getName());
                        }

                    }

                }
            }
        }

        //получаем причину отказа
        $lossReasonId = 0;
        $lossReasonName = " ";
        if($lead->getLossReasonId() != null){
            $lossReasonService = $apiClient->lossReasons();
            $lossReasonsCollection = $lossReasonService->get();
            foreach($lossReasonsCollection as $lossReason){
                if($lossReason->getId() == $lead->getLossReasonId()){
                    $lossReasonId = ($lossReason->getId()); //получаем его айдишник
                    $lossReasonName = ($lossReason->getName()); //получаем название причины отказа
                }
            }
        }

        //узнаем ресурсы для связи
        $sourceId = $lead->getSourceId();
        if($sourceId == null){
            $sourceId = 0;
        }
        $sourceName = " ";
        $sourceLink = " ";
        if($lead->getSourceId() !== null){
            $SourcesCollection = $apiClient->sources()->get();
            foreach($SourcesCollection as $Source){
                if($lead->getSourceId() == $Source->getId()){
                    $sourceName .= ($Source->getName());
                    $sourceLink .= ($Source->getLink());
                }
            }
        }


        //проверка тегов и целостность другиз полей
        $tagCollection = $lead->getTags();
        $tagsName = " ";
        $closestTaskAt = $lead->getClosestTaskAt();
        $customFieldsValues = $lead->getCustomFieldsValues();
        $score = $lead->getScore();
        $closed_at = $lead->getClosedAt();
        if($closestTaskAt == null){
            $closestTaskAt = 0;
        }
        if($customFieldsValues == null){
            $customFieldsValues = 0;
        }
        if(!$score){
            $score = " ";
        }
        if(!$closed_at){
            $closed_at = " ";
        }
        if($tagCollection != null){
            foreach ($tagCollection as $tag){
                $tagsName .= ($tag->getName());
            }
        }

        //компания
        $companyCollection = $lead->getCompany();
        $companyId = 0;
        $companyName = " ";
        $companyResponsibleUserId = 0;
        $companyGroupId = 0;
        $companyCreatedBy = " ";
        $companyUpdatedBy = " ";
        $companyCreatedAt = " ";
        $companyUpdatedAt = " ";
        $companyClosestTaskAt = " ";
        $companyCustomFieldsValues = " ";
        $companyAccountId = 0;
        if($companyCollection != null){
            foreach ($companyCollection as $company){
                $companyId = ($company->getId());
                $companyName = ($company->getName());
                $companyResponsibleUserId = ($company->getResponsibleUserId());
                $companyGroupId = ($company->getGroupId());
                $companyCreatedBy = ($company->getCreatedBy());
                $companyUpdatedBy = ($company->getUpdatedBy());
                $companyCreatedAt = ($company->getCreatedAt());
                $companyUpdatedAt = ($company->getUpdatedAt());
                $companyClosestTaskAt = ($company->getClosestTaskAt());
                $companyCustomFieldsValues = ($company->getCustomFieldsValues());
                $companyAccountId = ($company->getAccountId());
            }
        }


        if (Lead::where('id', $lead->getId())->count() < 1) {
            $leadAdd = Lead::create(array(
                "id" => $lead->getId(),
                "name" => $lead->getName(),
                "account_id" => $lead->getAccountId(),
                "price" => $lead->getPrice(),
                "responsible_user_name" => $userName,
                "group_id" => $lead->getGroupId(),
                "pipeline_id" => $pipelineId,
                "pipeline_name" => $pipelineName,
                "status_id" => $statusId,
                "status_name" => $statusName,
                "loss_reason_id" => $lossReasonId,
                "loss_reason_name" => $lossReasonName,
                "source_id" => $sourceId,
                "source_name" => $sourceName,
                "source_link" => $sourceLink,
                "created_by" => $lead->getCreatedBy(),
                "updated_by" => $lead->getUpdatedBy(),
                "created_at" => $lead->getCreatedAt(),
                "updated_at" => $lead->getUpdatedAt(),
                "closed_at" => $closed_at,
                "closest_task_at" => $closestTaskAt,
                "is_deleted" => $lead->getIsDeleted(),
                "custom_fields_values" => $customFieldsValues,
                "score" => $score,
                "tags_name" => $tagsName,
                "company_id" => $companyId,
                "company_name" => $companyName,
                "company_responsible_user_id" => $companyResponsibleUserId,
                "company_group_id" => $companyGroupId,
                "company_created_by" => $companyCreatedBy,
                "company_updated_by" => $companyUpdatedBy,
                "company_created_at" => $companyCreatedAt,
                "company_updated_at" => $companyUpdatedAt,
                "company_closest_task_at" => $companyClosestTaskAt,
                "company_custom_fields_values" => $companyCustomFieldsValues,
                "company_account_id" => $companyAccountId
            ));
        }

    }

    echo("Сделки и связанные с ними сущности выгружены в базу данных");

} catch (AmoCRMApiException $e) {
    echo('Сделки не найдены');
    die;
}



?>
