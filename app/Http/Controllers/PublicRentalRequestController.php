<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicRentalRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'space_id'             => 'required|exists:spaces,id',
            'category_id'          => 'nullable|exists:rental_categories,id',
            'preferred_date'       => 'required|date|after_or_equal:today',
            'preferred_start_time' => 'required|date_format:H:i',
            'duration_minutes'     => 'required|integer|min:15|max:480',
            'title'                => 'required|string|max:255',
            'renter_name'          => 'required|string|max:255',
            'renter_phone'         => 'required|string|max:40',
            'renter_email'         => 'nullable|email|max:255',
            'notes'                => 'nullable|string|max:2000',
            'consent'              => 'accepted',
        ], [
            'preferred_date.after_or_equal' => 'Дата не может быть в прошлом.',
            'space_id.required'             => 'Пожалуйста, выберите площадку.',
            'title.required'                => 'Укажите название команды или мероприятия.',
            'renter_name.required'          => 'Укажите ваше имя.',
            'renter_phone.required'         => 'Укажите телефон для связи.',
            'consent.accepted'              => 'Необходимо ваше согласие на обработку персональных данных.',
        ]);

        DB::table('rental_requests')->insert([
            'space_id'             => (int) $data['space_id'],
            'category_id'          => $data['category_id'] ?? null,
            'preferred_date'       => $data['preferred_date'],
            'preferred_start_time' => $data['preferred_start_time'] . ':00',
            'duration_minutes'     => (int) $data['duration_minutes'],
            'title'                => trim($data['title']),
            'renter_name'          => trim($data['renter_name']),
            'renter_phone'         => trim($data['renter_phone']),
            'renter_email'         => $data['renter_email'] ?? null,
            'notes'                => $data['notes'] ?? null,
            'status'               => 'new',
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        return redirect()->to(url('/') . '#rental-request')->with('rental_success', 'Заявка отправлена! Мы свяжемся с вами.');
    }
}