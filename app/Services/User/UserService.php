<?php

namespace App\Services\User;

use App\Exceptions\UserAlreadyExistException;
use Illuminate\Support\Facades\Log;
use App\Lib\Transaction\TransactionInterface;

use App\Models\Auth;
use App\Models\BodyPart;
use App\Models\ExerciseType;
use App\Models\User;
use Illuminate\Database\QueryException;

use App\Services\BaseService;
use Domain\DomainServices\UserDomainService;

class UserService extends BaseService
{
    private TransactionInterface $transaction;

    private Auth $authModel;
    private BodyPart $bodyPartModel;
    private ExerciseType $exerciseTypeModel;
    private User $userModel;
    private UserDomainService $userDomainService;

    /**
     * UserService constructor.
     * @param $transaction
     * @param Auth $authModel
     * @param BodyPart $bodyPartModel
     * @param ExerciseType $exerciseTypeModel
     * @param User $userModel
     * @param UserDomainService $userDomainService
     */
    public function __construct(
        TransactionInterface $transaction,
        Auth $authModel,
        BodyPart $bodyPartModel,
        ExerciseType $exerciseTypeModel,
        User $userModel,
        UserDomainService $userDomainService
    ) {
        $this->transaction = $transaction;
        $this->authModel = $authModel;
        $this->bodyPartModel = $bodyPartModel;
        $this->exerciseTypeModel = $exerciseTypeModel;
        $this->userModel = $userModel;
        $this->userDomainService = $userDomainService;
    }


    /**
     * @param int $user_id
     * @return \Illuminate\Database\Eloquent\Model|null|User
     */
    final public function getUser(int $user_id)
    {
        return $this->userModel->newQuery()->find($user_id);
    }

    /**
     * @param int $auth_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null|User
     */
    final public function getUserByAuthId(int $auth_id)
    {
        return $this->userModel->newQuery()->where('auth_id', $auth_id)->first();
    }


    /**
     * 新しくユーザーを登録する
     * @param string $name
     * @param $birth_day
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|Auth $auth
     * @throws \Exception
     */
    final public function createUser(string $name,  $birth_day, $auth): void
    {
        $this->transaction->begin();
        try {

            $is_exist = $this->userDomainService->isExist($auth);
            if ($is_exist) {
                throw new UserAlreadyExistException($this->trans_message('messages.user.already_exist'));
            }

            $user = $this->userModel->newQuery()->create(
                [
                    'auth_id' => $auth->id,
                    'name' => $name,
                    'birth_day' => $birth_day
                ]
            );

            $this->initializeExerciseType($user);

            $this->transaction->commit();
        } catch (UserAlreadyExistException $error) {
            Log::error($error->getMessage());
            $this->transaction->rollBack();
            abort(409, $error->getMessage());
        } catch (QueryException $error) {
            Log::error($error->getMessage());
            $this->transaction->rollBack();
            throw new \Exception($this->trans_message('messages.register_failed'));
        }
    }

    /**
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getAuth(User $user)
    {
        return $this->authModel->newQuery()->find($user->auth_id);
    }

    /**
     * トレーニング種目を初期化する
     * @param $user
     */
    final private function initializeExerciseType($user) : void
    {
        $body_parts = $this->bodyPartModel->newQuery()->get();
        $exercise_types = config('default.EXERCISE_TYPES');

        foreach ($body_parts as $index => $body_part) {
            foreach ($exercise_types[$index] as $j_index => $exercise_type) {
                $this->exerciseTypeModel->newQuery()->create(
                    [
                        'body_part_id' => $body_part->id,
                        'user_id' => $user->id,
                        'name' => $exercise_type,
                        'sort_index' => $j_index + 1
                    ]
                );
            }
        }
    }
}
