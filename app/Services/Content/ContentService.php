<?php

namespace App\Services\Content;

use App\Models\Content\Content;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use PhpParser\Node\Expr\FuncCall;

class ContentService{

    /**
     * get all contents.
     *
     * @param
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Content[]
     */
    public function getAllContents() {
        return Content::all();
    }

     /**
     * Create new content.
     *
     * @param array $data
     * @return \App\Models\Content
     */
    public function saveContent(array $data) {

        return DB::transaction(function() use ($data){
            return Content::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'bibliography' => $data['bibliography'],
                'order' => $data['order'],
                'course_id' => $data['course_id'],
                'grade_id' => $data['grade_id'],
            ]);
        });
    }

    /**
     * Get content by id.
     *
     * @param $data
     * @return \App\Models\Content
     */
    public function showContent($data)
    {
        $result = Content::findOrFail($data["content_id"]);
        return $result;
    }

     /**
     * Update content.
     *
     * @param array $data
     * @return \App\Models\Content
     */
    public function updateContent(array $data, $id) {

        $content = Content::findOrFail($id);

        return DB::transaction(function() use ($content, $data){
            $updates =  [
                'name' => $data['name'] ?? $content->name,
                'description' => $data['description'] ?? $content->description,
                'bibliography' => $data['bibliography'] ?? $content->bibliography,
                'order' => $data['order'] ?? $content->order,
                'course_id' => $data['course_id'] ?? $content->course_id,
                'grade_id' => $data['grade_id'] ?? $content->grade_id,
            ];
            $content->update($updates);
            return $content;
        });
    }

    public function deleteContent($id)
    {
        DB::transaction(function() use ($id) {
            $content = Content::findOrFail($id);
            $content->delete();
        });
    }

}