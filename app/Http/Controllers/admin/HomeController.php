<?php

namespace App\Http\Controllers\admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $totalOrders = Order::where('status', '!=', 'cancelled')->count();
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 1)->count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('grand_total');
        //This month revenue

        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentDate = Carbon::now()->format('Y-m-d');
        $revenueThisMonth = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $startOfMonth)
            ->whereDate('created_at', '<=', $currentDate)
            ->sum('grand_total');

        //Last Month Revenue

        $lastMonthName = Carbon::now()->subMonth()->startOfMonth()->format('M');

        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
        $revenueLastsMonth = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $lastMonthStart)
            ->whereDate('created_at', '<=', $lastMonthEnd)
            ->sum('grand_total');

        //last 30 days revenue

        $date = Carbon::now()->subDays(30)->format('Y-m-d');
        $revenueLast30Days = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $date)
            ->whereDate('created_at', '<=', $currentDate)
            ->sum('grand_total');

        return view('admin.dashboard', [
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalCustomers' => $totalCustomers,
            'totalRevenue' => $totalRevenue,
            'revenueThisMonth' => $revenueThisMonth,
            'revenueLastsMonth' => $revenueLastsMonth,
            'revenueLast30Days' => $revenueLast30Days,
            'lastMonthName' => $lastMonthName,
        ]);
    }
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
