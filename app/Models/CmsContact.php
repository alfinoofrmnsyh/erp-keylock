<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsContact extends Model
{
    protected $table = 'cms_contacts';

    protected $fillable = [
        'contact1',
        'contact2',
        'wechat_qr',
    ];
}
