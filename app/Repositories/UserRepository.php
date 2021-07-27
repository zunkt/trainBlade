<?php

namespace App\Repositories;

use App\User;
use Illuminate\Http\Request;

class UserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name', 'email', 'password',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }

    public function getFieldsSearchable()
    {
        // TODO: Implement getFieldsSearchable() method.
        return $this->fieldSearchable;
    }

    public function userSearch(Request $request)
    {
        $query = $this->model->newQuery();

        if (isset($request->filters) && ! empty($request->filters)) {
            foreach ($request->filters as $filter) {
                $query->where($filter['field'], $filter['type'], '%'.$filter['value'].'%');
            }
        }

        if (isset($request->sorters) && ! empty($request->sorters)) {
            foreach ($request->sorters as $sort) {
                $query->orderBy($sort['field'], $sort['dir']);
            }
        }

        if (isset($request->approved)) {
            if ($request->approved == 1) {
                $query->whereNotNull('approved_at');
            } else {
                $query->whereNull('approved_at');
            }
        }

        if (isset($request->name)) {
            $query->where('name', 'like', '%' . urldecode($request->name) . '%');
        }

        return $query;
    }
}
