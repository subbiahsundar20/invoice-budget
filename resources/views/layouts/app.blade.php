
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Invoice & Budget Manager')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex">
        <aside class="sidebar bg-dark text-white p-3">
            <h5 class="mb-4">Finance Manager</h5>
            <hr>

            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('customers.index') }}">Customers</a>
            <a href="{{ route('suppliers.index') }}">Suppliers</a>
            <a href="{{ route('products.index') }}">Products</a>
            <a href="{{ route('sales-invoices.index') }}">Sales Invoices</a>
            <a href="{{ route('purchase-invoices.index') }}">Purchase Invoices</a>
            <a href="{{ route('budgets.index') }}">Budgets</a>
            <a href="{{ route('expenses.index') }}">Expenses</a>
            <a href="{{ route('reports.index') }}"
                class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i>
                    Reports
                </a>
        </aside>

        <main class="dashboard-content flex-grow-1 p-4">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <h4>@yield('heading', 'Dashboard')</h4>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        Logout
                    </button>
                </form>
            </header>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
