<!-- Sidebar -->
<aside class="sidebar sidebar-expand-lg sidebar-dark">
    <header class="sidebar-header bg-dark">
        <a class="logo-icon" href="{{ route('admin') }}"><img src="{{ asset('dashboard/images/logo-light-lg.png') }}"
                alt="logo icon"></a>
        <span class="logo">
            <a href="{{ route('admin') }}">
                <img src="{{ asset('dashboard/images/Final-Logo03.png') }}" alt="logo">
            </a>
        </span>
        <span class="sidebar-toggle-fold"></span>
    </header>

    <nav class="sidebar-navigation">

        {{-- <li class="menu-item {{ Request::routeIs('purchase.*') ? 'active open' : '' }}">
          <a class="menu-link" href="#">
            <span class="icon fa fa-cog"></span>
            <span class="title">Setup</span>
            <span class="arrow"></span>
          </a>

          <ul class="menu-submenu">
            <li class="menu-item {{ Request::routeIs('payment_method.index') ? 'active' : '' }}">
              <a class="menu-link" href="{{ route('purchase.create') }}">
                <span class="dot"></span>
                <span class="title">Add Purchase</span>
              </a>
            </li>

            <li class="menu-item">
              <a class="menu-link" href="{{ route('purchase.index') }}">
                <span class="dot"></span>
                <span class="title">Manage Purchases</span>
              </a>
            </li>
          </ul>

        </li> --}}

        <ul class="menu">
            <li class="menu-item {{ Request::routeIs('admin') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route('admin') }}">
                    <span class="icon fa fa-home"></span>
                    <span class="title">Dashboard</span>
                </a>
            </li>

            @can('list-owner')
                <li class="menu-item {{ Request::routeIs('owners.index') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('owners.index') }}">
                        <img src="{{ asset('dashboard/sidebar_icons/suited_man.svg') }}" alt=""
                            class="sidebar_icon icon">
                        <span class="title">Owners</span>
                    </a>
                </li>
            @endcan

            @can('list-bank_account')
                <li class="menu-item {{ Request::routeIs('bank_account.index') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('bank_account.index') }}">
                        <img src="{{ asset('dashboard/sidebar_icons/bank_card.svg') }}" alt=""
                            class="sidebar_icon icon">
                        <span class="title">Bank Accounts</span>
                    </a>
                </li>
            @endcan

            @canany(['list-pos', 'create-pos', 'list-purchase', 'create-purchase', 'list-return'])
                <li class="menu-category">Sale & Purchase</li>
                @canany(['list-pos', 'create-pos'])
                    @can('create-pos')
                        <li class="menu-item {{ Request::routeIs('pos.create') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('pos.create') }}">
                                <span class="icon fa fa-cart-plus"></span>
                                <span class="title"> POS</span>
                            </a>
                        </li>
                    @endcan
                    @can('list-pos')
                        <li class="menu-item {{ Request::routeIs('pos.index') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('pos.index') }}">
                                <span class="icon fa fa-shopping-bag"></span>
                                <span class="title"> Sales</span>
                            </a>
                        </li>
                    @endcan
                    @can('list-return')
                        <li class="menu-item {{ Request::routeIs('return.index') ? 'active' : '' }}">
                            <a class="menu-link" href="{{ route('return.index') }}">
                                <img src="{{ asset('dashboard/sidebar_icons/return_box.svg') }}" alt=""
                                    class="sidebar_icon icon">
                                <span class="title"> Returns</span>
                            </a>
                        </li>
                    @endcan
                @endcanany


                @canany(['list-purchase', 'create-purchase'])
                    <li class="menu-item {{ Request::routeIs('purchase.*') ? 'active open' : '' }}">
                        <a class="menu-link" href="#">
                            <img src="{{ asset('dashboard/sidebar_icons/purchase_cart.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Purchase</span>
                            <span class="arrow"></span>
                        </a>
                        @can('create-purchase')
                            <ul class="menu-submenu">
                                <li class="menu-item {{ Request::routeIs('purchase.create') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('purchase.create') }}">
                                        <span class="dot"></span>
                                        <span class="title">Add Purchase</span>
                                    </a>
                                </li>
                            @endcan
                            @can('list-purchase')
                                <li class="menu-item">
                                    <a class="menu-link" href="{{ route('purchase.index') }}">
                                        <span class="dot"></span>
                                        <span class="title">Manage Purchases</span>
                                    </a>
                                </li>
                            </ul>
                        @endcan

                    </li>
                @endcanany
            @endcanany
            {{-- Single Item (Stock) --}}
            @can('stock')
                <li class="menu-item {{ Request::routeIs('stock.index') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('stock.index') }}">
                        <img src="{{ asset('dashboard/sidebar_icons/stock_box_tick.svg') }}" alt=""
                            class="sidebar_icon icon">
                        <span class="title"> Stock</span>
                    </a>
                </li>
            @endcan

            @canany(['list-damage', 'create-damage'])
                <li class="menu-item {{ Request::routeIs('damage.*') ? 'active open' : '' }}">
                    <a class="menu-link" href="#">
                        <img src="{{ asset('dashboard/sidebar_icons/damage.svg') }}" alt=""
                            class="sidebar_icon icon">
                        <span class="title">Damages</span>
                        <span class="arrow"></span>
                    </a>

                    <ul class="menu-submenu">
                        @can('create-damage')
                            <li class="menu-item {{ Request::routeIs('damage.create') ? 'active' : '' }}">
                                <a class="menu-link" href="{{ route('damage.create') }}">
                                    <span class="dot"></span>
                                    <span class="title">Add Damage</span>
                                </a>
                            </li>
                        @endcan

                        @can('list-damage')
                            <li class="menu-item {{ Request::routeIs('damage.index') ? 'active' : '' }}">
                                <a class="menu-link" href="{{ route('damage.index') }}">
                                    <span class="dot"></span>
                                    <span class="title">Damages</span>
                                </a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcanany

            @canany(['list-unit', 'create-unit', 'list-product', 'create-product', 'list-category', 'create-category',
                'list-brand', 'create-brand'])
                <li class="menu-category">Product Information</li>

                @canany(['list-unit', 'create-unit'])
                    <li class="menu-item {{ Request::routeIs('unit.*') ? 'active open' : '' }}">
                        <a class="menu-link" href="#">
                            <img src="{{ asset('dashboard/sidebar_icons/unit_kg.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Units</span>
                            <span class="arrow"></span>
                        </a>

                        <ul class="menu-submenu">
                            @can('create-unit')
                                <li class="menu-item {{ Request::routeIs('unit.create') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('unit.create') }}">
                                        <span class="dot"></span>
                                        <span class="title">Add Unit</span>
                                    </a>
                                </li>
                            @endcan

                            @can('list-unit')
                                <li class="menu-item {{ Request::routeIs('unit.index') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('unit.index') }}">
                                        <span class="dot"></span>
                                        <span class="title">Manage Units</span>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcanany

                @canany(['list-product', 'create-product'])
                    <li class="menu-item {{ Request::routeIs('product.*') ? 'active open' : '' }}">
                        <a class="menu-link" href="#">
                            <img src="{{ asset('dashboard/sidebar_icons/product_dairy.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Products</span>
                            <span class="arrow"></span>
                        </a>

                        <ul class="menu-submenu">
                            @can('create-product')
                                <li class="menu-item {{ Request::routeIs('product.create') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('product.create') }}">
                                        <span class="dot"></span>
                                        <span class="title">Add Product</span>
                                    </a>
                                </li>
                            @endcan

                            @can('list-product')
                                <li class="menu-item {{ Request::routeIs('product.index') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('product.index') }}">
                                        <span class="dot"></span>
                                        <span class="title">Manage Products</span>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcanany

                @canany(['list-category', 'create-category'])
                    <li class="menu-item {{ Request::routeIs('category.*') ? 'active open' : '' }}">
                        <a class="menu-link" href="#">
                            <img src="{{ asset('dashboard/sidebar_icons/categories_4_box.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Categories</span>
                            <span class="arrow"></span>
                        </a>

                        <ul class="menu-submenu">
                            @can('create-category')
                                <li class="menu-item {{ Request::routeIs('category.create') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('category.create') }}">
                                        <span class="dot"></span>
                                        <span class="title">Add Category</span>
                                    </a>
                                </li>
                            @endcan

                            @can('list-category')
                                <li class="menu-item {{ Request::routeIs('category.index') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('category.index') }}">
                                        <span class="dot"></span>
                                        <span class="title">Manage Categories</span>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcanany

                @canany(['list-brand', 'create-brand'])
                    <li class="menu-item {{ Request::routeIs('brand.*') ? 'active open' : '' }}">
                        <a class="menu-link" href="#">
                            <img src="{{ asset('dashboard/sidebar_icons/brand.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Brands</span>
                            <span class="arrow"></span>
                        </a>

                        <ul class="menu-submenu">
                            @can('create-brand')
                                <li class="menu-item {{ Request::routeIs('brand.create') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('brand.create') }}">
                                        <span class="dot"></span>
                                        <span class="title">Add Brand</span>
                                    </a>
                                </li>
                            @endcan

                            @can('list-brand')
                                <li class="menu-item {{ Request::routeIs('brand.index') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('brand.index') }}">
                                        <span class="dot"></span>
                                        <span class="title">Manage Brands</span>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcanany
            @endcanany

            @canany(['list-expense', 'create-expense', 'list-payment', 'create-payment', 'list-expense_category'])
                <li class="menu-category">Expenses & Payment</li>

                @canany(['list-expense', 'create-expense', 'list-expense_category'])
                    <li class="menu-item">
                        <a class="menu-link" href="#">
                            <img src="{{ asset('dashboard/sidebar_icons/expense.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Expenses</span>
                            <span class="arrow"></span>
                        </a>

                        <ul class="menu-submenu">
                            @can('create-expense')
                                <li class="menu-item {{ Request::routeIs('expense.create') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('expense.create') }}">
                                        <span class="dot"></span>
                                        <span class="title">Add Expense</span>
                                    </a>
                                </li>
                            @endcan

                            @can('list-expense')
                                <li class="menu-item {{ Request::routeIs('expense.index') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('expense.index') }}">
                                        <span class="dot"></span>
                                        <span class="title">Manage Expenses</span>
                                    </a>
                                </li>
                            @endcan

                            @can('list-expense_category')
                                <li class="menu-item {{ Request::routeIs('expense-category') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('expense-category.index') }}">
                                        <span class="dot"></span>
                                        <span class="title">Expense Category</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['list-payment', 'create-payment'])
                    <li class="menu-item">
                        <a class="menu-link" href="#">
                            <img src="{{ asset('dashboard/sidebar_icons/payments.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Payments</span>
                            <span class="arrow"></span>
                        </a>

                        <ul class="menu-submenu">
                            @can('create-payment')
                                <li class="menu-item {{ Request::routeIs('payment.create') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('payment.create') }}">
                                        <span class="dot"></span>
                                        <span class="title">Add Payment</span>
                                    </a>
                                </li>
                            @endcan

                            @can('list-payment')
                                <li class="menu-item {{ Request::routeIs('payment.index') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('payment.index') }}">
                                        <span class="dot"></span>
                                        <span class="title">Payments</span>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcanany

            @endcanany

            @can('promotional_sms')
                <li class="menu-category">Promotion </li>
                <li class="menu-item {{ Request::routeIs('promotion.sms') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('promotion.sms') }}">
                        <span class="icon fa fa-envelope"></span>
                        <span class="title"> Promotional SMS </span>
                    </a>
                </li>
            @endcan

            {{-- <li class="menu-item {{ Request::routeIs('payment.*') ? 'active' : '' }}">
                  <a class="menu-link" href="{{ route('payment.index') }}">
                    <span class="icon fa fa-money"></span>
                    <span class="title"> Payment</span>
                  </a>
                </li> --}}

            @canany(['list-customer', 'create-customer', 'list-supplier', 'create-supplier'])
                <li class="menu-category">Peoples</li>

                {{-- <li class="menu-item {{ Request::routeIs('user.*') ? 'active open' : '' }}">
                  <a class="menu-link" href="#">
                    <span class="icon fa fa-folder"></span>
                    <span class="title">Users</span>
                    <span class="arrow"></span>
                  </a>

                  <ul class="menu-submenu">
                    <li class="menu-item {{ Request::routeIs('user.index') ? 'active' : '' }}">
                      <a class="menu-link" href="{{ route('user.index') }}">
                        <span class="dot"></span>
                        <span class="title">User Manage</span>
                      </a>
                    </li>
                  </ul>
                </li> --}}
                @canany(['list-customer', 'create-customer'])
                    <li class="menu-item {{ Request::routeIs('customer.*') ? 'active open' : '' }}">
                        <a class="menu-link" href="#">
                            <img src="{{ asset('dashboard/sidebar_icons/customers.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Customers</span>
                            <span class="arrow"></span>
                        </a>

                        <ul class="menu-submenu">
                            @can('create-customer')
                                <li class="menu-item {{ Request::routeIs('customer.create') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('customer.create') }}">
                                        <span class="dot"></span>
                                        <span class="title">Add Customer</span>
                                    </a>
                                </li>
                            @endcan
                            @can('list-customer')
                                <li class="menu-item {{ Request::routeIs('customer.index') ? 'active' : '' }}">
                                    <a class="menu-link" href="{{ route('customer.index') }}">
                                        <span class="dot"></span>
                                        <span class="title">Manage Customers</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['list-supplier', 'create-supplier'])
                    <li class="menu-item {{ Request::routeIs('supplier.*') ? 'active open' : '' }}">
                        <a class="menu-link" href="#">
                            {{-- <span class="icon fa fa-wheelchair-alt"></span> --}}
                            <img src="{{ asset('dashboard/sidebar_icons/supplier_product.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Suppliers</span>
                            <span class="arrow"></span>
                        </a>

                        <ul class="menu-submenu">
                            @can('create-supplier')
                                <li class="menu-item">
                                    <a class="menu-link {{ Request::routeIs('supplier.create') ? 'active' : '' }}"
                                        href="{{ route('supplier.create') }}">
                                        <span class="dot"></span>
                                        <span class="title">Add Supplier</span>
                                    </a>
                                </li>
                            @endcan

                            @can('list-supplier')
                                <li class="menu-item">
                                    <a class="menu-link {{ Request::routeIs('supplier.index') ? 'active' : '' }}"
                                        href="{{ route('supplier.index') }}">
                                        <span class="dot"></span>
                                        <span class="title">Manage Suppliers</span>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcanany
            @endcanany


            @canany(['today_report', 'current_month_report', 'summary_report', 'daily_report', 'customer_due_report',
                'supplier_due_report', 'low_stock_report', 'top_customer_report', 'top_product_report',
                'top_product_all_time_report', 'purchase_report', 'customer_ledger', 'supplier_ledger',
                'profit_loss_report'])
                <li class="menu-category">Reports</li>

                @can('profit_loss_report')
                    <li class="menu-item {{ Request::routeIs('report.profit_loss_report') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.profit_loss_report') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/profit.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Profit Loss Report</span>
                        </a>
                    </li>
                @endcan

                @can('today_report')
                    <li class="menu-item {{ Request::routeIs('today_report') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('today_report') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/24hr.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title"> Today Report</span>
                        </a>
                    </li>
                @endcan

                @can('current_month_report')
                    <li class="menu-item {{ Request::routeIs('current_month_report') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('current_month_report') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/calendar30.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title"> Current Month Report</span>
                        </a>
                    </li>
                @endcan

                @can('summary_report')
                    <li class="menu-item {{ Request::routeIs('summary_report') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('summary_report') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/report_timeperiod.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title"> Summary Report</span>
                        </a>
                    </li>
                @endcan

                @can('daily_report')
                    <li class="menu-item {{ Request::routeIs('daily_report') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('daily_report') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/calendar_time_period.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title"> Daily Report</span>
                        </a>
                    </li>
                @endcan

                @can('customer_due_report')
                    <li class="menu-item {{ Request::routeIs('report.customer_due') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.customer_due') }}">

                            <img src="{{ asset('dashboard/sidebar_icons/money_list.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Customer Due Report</span>
                        </a>
                    </li>
                @endcan

                @can('supplier_due_report')
                    <li class="menu-item {{ Request::routeIs('report.supplier_due') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.supplier_due') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/money_list_tick_box.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Supplier Due Report</span>
                        </a>
                    </li>
                @endcan

                @can('low_stock_report')
                    <li class="menu-item {{ Request::routeIs('report.low_stock') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.low_stock') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/box_down_arrow.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Low Stock Report</span>
                        </a>
                    </li>
                @endcan

                @can('top_customer_report')
                    <li class="menu-item {{ Request::routeIs('report.top_customer') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.top_customer') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/person_star.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Top Customer</span>
                        </a>
                    </li>
                @endcan

                @can('top_product_report')
                    <li class="menu-item {{ Request::routeIs('report.top_product') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.top_product') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/box_list.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Top Product</span>
                        </a>
                    </li>
                @endcan

                @can('top_product_all_time_report')
                    <li class="menu-item {{ Request::routeIs('report.top_product_all_time') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.top_product_all_time') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/list_high_to_low.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Top Product - All Time</span>
                        </a>
                    </li>
                @endcan


                @can('purchase_report')
                    <li class="menu-item {{ Request::routeIs('report.purchase_report') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.purchase_report') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/boxes_board_list.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Purchase Report</span>
                        </a>
                    </li>
                @endcan

                @can('customer_ledger')
                    <li class="menu-item {{ Request::routeIs('report.customer_ledger') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.customer_ledger') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/calculator_table.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Customer Ledger</span>
                        </a>
                    </li>
                @endcan

                @can('supplier_ledger')
                    <li class="menu-item {{ Request::routeIs('report.supplier_ledger') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('report.supplier_ledger') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/table_money.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title">Supplier Ledger</span>
                        </a>
                    </li>
                @endcan

            @endcanany




            @canany(['setting', 'backup', 'list-role', 'list-user'])
                <li class="menu-category">Setting & Customize</li>

                @can('setting')
                    <li class="menu-item {{ Request::routeIs('pos.pos_setting') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('pos.pos_setting') }}">
                            <span class="icon fa fa-wrench"></span>
                            <span class="title"> Settings</span>
                        </a>
                    </li>
                @endcan

                @can('list-role')
                    <li class="menu-item {{ Request::routeIs('roles.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('roles.index') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/user_role.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title"> Roles & Permissions</span>
                        </a>
                    </li>
                @endcan

                @can('list-user')
                    <li class="menu-item {{ Request::routeIs('users.index') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('users.index') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/users.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title"> Users</span>
                        </a>
                    </li>
                @endcan


                @can('backup')
                    <li class="menu-item {{ Request::routeIs('backup') ? 'active' : '' }}">
                        <a class="menu-link" href="{{ route('backup') }}">
                            <img src="{{ asset('dashboard/sidebar_icons/box_download.svg') }}" alt=""
                                class="sidebar_icon icon">
                            <span class="title"> Backup</span>
                        </a>
                    </li>
                @endcan
            @endcanany
        </ul>
    </nav>

</aside>
<!-- END Sidebar -->
