create table cache
(
    `key`      varchar(255) not null
        primary key,
    value      mediumtext   not null,
    expiration int          not null
)
    collate = utf8mb4_unicode_ci;

create table cache_locks
(
    `key`      varchar(255) not null
        primary key,
    owner      varchar(255) not null,
    expiration int          not null
)
    collate = utf8mb4_unicode_ci;

create table categories
(
    id         bigint unsigned auto_increment
        primary key,
    name       varchar(255)                                 not null,
    icon_type  enum ('default', 'custom') default 'default' not null,
    icon       varchar(255)                                 null,
    created_at timestamp                                    null,
    updated_at timestamp                                    null
)
    collate = utf8mb4_unicode_ci;

create table expenses
(
    id          bigint unsigned auto_increment
        primary key,
    description varchar(255)   not null,
    amount      decimal(10, 2) not null,
    created_at  timestamp      null,
    updated_at  timestamp      null
)
    collate = utf8mb4_unicode_ci;

create table failed_jobs
(
    id         bigint unsigned auto_increment
        primary key,
    uuid       varchar(255)                        not null,
    connection text                                not null,
    queue      text                                not null,
    payload    longtext                            not null,
    exception  longtext                            not null,
    failed_at  timestamp default CURRENT_TIMESTAMP not null,
    constraint failed_jobs_uuid_unique
        unique (uuid)
)
    collate = utf8mb4_unicode_ci;

create table job_batches
(
    id             varchar(255) not null
        primary key,
    name           varchar(255) not null,
    total_jobs     int          not null,
    pending_jobs   int          not null,
    failed_jobs    int          not null,
    failed_job_ids longtext     not null,
    options        mediumtext   null,
    cancelled_at   int          null,
    created_at     int          not null,
    finished_at    int          null
)
    collate = utf8mb4_unicode_ci;

create table jobs
(
    id           bigint unsigned auto_increment
        primary key,
    queue        varchar(255)     not null,
    payload      longtext         not null,
    attempts     tinyint unsigned not null,
    reserved_at  int unsigned     null,
    available_at int unsigned     not null,
    created_at   int unsigned     not null
)
    collate = utf8mb4_unicode_ci;

create index jobs_queue_index
    on jobs (queue);

create table manufacturers
(
    id             bigint unsigned auto_increment
        primary key,
    name           varchar(255)         not null,
    contact_person varchar(255)         null,
    phone          varchar(255)         null,
    email          varchar(255)         null,
    address        text                 null,
    notes          text                 null,
    is_active      tinyint(1) default 1 not null,
    created_at     timestamp            null,
    updated_at     timestamp            null,
    constraint manufacturers_name_unique
        unique (name)
)
    collate = utf8mb4_unicode_ci;

create table migrations
(
    id        int unsigned auto_increment
        primary key,
    migration varchar(255) not null,
    batch     int          not null
)
    collate = utf8mb4_unicode_ci;

create table password_reset_tokens
(
    email      varchar(255) not null
        primary key,
    token      varchar(255) not null,
    created_at timestamp    null
)
    collate = utf8mb4_unicode_ci;

create table products
(
    id              bigint unsigned auto_increment
        primary key,
    category_id     bigint unsigned             not null,
    manufacturer_id bigint unsigned             null,
    name            varchar(255)                not null,
    description     text                        null,
    price           decimal(10, 2) default 0.00 not null,
    barcode         varchar(255)                null,
    created_at      timestamp                   null,
    updated_at      timestamp                   null,
    constraint products_barcode_unique
        unique (barcode),
    constraint products_category_id_foreign
        foreign key (category_id) references categories (id)
            on delete cascade,
    constraint products_manufacturer_id_foreign
        foreign key (manufacturer_id) references manufacturers (id)
            on delete set null
)
    collate = utf8mb4_unicode_ci;

create table sessions
(
    id            varchar(255)    not null
        primary key,
    user_id       bigint unsigned null,
    ip_address    varchar(45)     null,
    user_agent    text            null,
    payload       longtext        not null,
    last_activity int             not null
)
    collate = utf8mb4_unicode_ci;

create index sessions_last_activity_index
    on sessions (last_activity);

create index sessions_user_id_index
    on sessions (user_id);

create table settings
(
    id         bigint unsigned auto_increment
        primary key,
    name       varchar(255) null,
    address    varchar(255) null,
    phone      varchar(255) null,
    email      varchar(255) null,
    created_at timestamp    null,
    updated_at timestamp    null
)
    collate = utf8mb4_unicode_ci;

create table stocks
(
    id             bigint unsigned auto_increment
        primary key,
    product_id     bigint unsigned                                not null,
    unit           enum ('adet', 'kg', 'lt', 'gr') default 'adet' not null,
    quantity       int                             default 0      not null,
    supplier       varchar(255)                                   null,
    purchase_price decimal(10, 2)                                 null,
    sale_price     decimal(10, 2)                                 null,
    created_at     timestamp                                      null,
    updated_at     timestamp                                      null,
    constraint stocks_product_id_foreign
        foreign key (product_id) references products (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;

create table recipes
(
    id         bigint unsigned auto_increment
        primary key,
    product_id bigint unsigned                                not null,
    stock_id   bigint unsigned                                not null,
    quantity   decimal(10, 2)                                 not null,
    unit       enum ('adet', 'kg', 'lt', 'gr') default 'adet' not null,
    created_at timestamp                                      null,
    updated_at timestamp                                      null,
    constraint recipes_product_id_foreign
        foreign key (product_id) references products (id)
            on delete cascade,
    constraint recipes_stock_id_foreign
        foreign key (stock_id) references stocks (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;

create table suppliers
(
    id             bigint unsigned auto_increment
        primary key,
    name           varchar(255)         not null,
    contact_person varchar(255)         null,
    phone          varchar(255)         null,
    email          varchar(255)         null,
    address        text                 null,
    notes          text                 null,
    is_active      tinyint(1) default 1 not null,
    created_at     timestamp            null,
    updated_at     timestamp            null
)
    collate = utf8mb4_unicode_ci;

create table support_messages
(
    id         bigint unsigned auto_increment
        primary key,
    fullname   varchar(255) not null,
    phone      varchar(255) null,
    email      varchar(255) null,
    subject    varchar(255) not null,
    message    text         not null,
    created_at timestamp    null,
    updated_at timestamp    null
)
    collate = utf8mb4_unicode_ci;

create table users
(
    id                bigint unsigned auto_increment
        primary key,
    name              varchar(255)                                                                     not null,
    lastName          varchar(255)                                                                     null,
    email             varchar(255)                                                                     not null,
    email_verified_at timestamp                                                                        null,
    password          varchar(255)                                                                     not null,
    phone             varchar(11)                                                default '00000000000' not null,
    role              enum ('admin', 'waiter', 'kitchen', 'cashier', 'customer') default 'customer'    not null,
    remember_token    varchar(100)                                                                     null,
    created_at        timestamp                                                                        null,
    updated_at        timestamp                                                                        null,
    constraint users_email_unique
        unique (email)
)
    collate = utf8mb4_unicode_ci;

create table customers
(
    id         bigint unsigned auto_increment
        primary key,
    user_id    bigint unsigned null,
    name       varchar(255)    null,
    created_at timestamp       null,
    updated_at timestamp       null,
    constraint customers_user_id_foreign
        foreign key (user_id) references users (id)
            on delete set null
)
    collate = utf8mb4_unicode_ci;

create table tables
(
    id                bigint unsigned auto_increment
        primary key,
    table_number      varchar(255)                       not null,
    qr_code           varchar(255)                       not null,
    status            enum ('boş', 'dolu') default 'boş' not null,
    status_changed_at timestamp                          null,
    capacity          int                  default 4     not null,
    waiter_id         bigint unsigned                    null,
    created_at        timestamp                          null,
    updated_at        timestamp                          null,
    constraint tables_qr_code_unique
        unique (qr_code),
    constraint tables_table_number_unique
        unique (table_number),
    constraint tables_waiter_id_foreign
        foreign key (waiter_id) references users (id)
            on delete set null
)
    collate = utf8mb4_unicode_ci;

create table orders
(
    id             bigint unsigned auto_increment
        primary key,
    customer_id    bigint unsigned                                                                                            not null,
    table_id       bigint unsigned                                                                                            not null,
    status         enum ('sipariş alındı', 'hazırlanıyor', 'hazır', 'teslim edildi', 'iptal edildi') default 'sipariş alındı' not null,
    payment_status enum ('bekliyor', 'ödendi', 'iptal edildi')                                       default 'bekliyor'       not null,
    created_at     timestamp                                                                                                  null,
    updated_at     timestamp                                                                                                  null,
    constraint orders_customer_id_foreign
        foreign key (customer_id) references customers (id)
            on delete cascade,
    constraint orders_table_id_foreign
        foreign key (table_id) references tables (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;

create table notifications
(
    id          bigint unsigned auto_increment
        primary key,
    order_id    bigint unsigned                                               not null,
    customer_id bigint unsigned                                               null,
    type        enum ('sipariş', 'ödeme', 'stok', 'genel') default 'genel'    not null,
    message     varchar(255)                                                  not null,
    status      enum ('okundu', 'okunmadı')                default 'okunmadı' not null,
    created_at  timestamp                                                     null,
    updated_at  timestamp                                                     null,
    constraint notifications_customer_id_foreign
        foreign key (customer_id) references customers (id)
            on delete cascade,
    constraint notifications_order_id_foreign
        foreign key (order_id) references orders (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;

create table order_details
(
    id           bigint unsigned auto_increment
        primary key,
    order_id     bigint unsigned      not null,
    product_id   bigint unsigned      not null,
    quantity     int                  not null,
    price        decimal(10, 2)       not null,
    is_delivered tinyint(1) default 0 not null,
    is_paid      tinyint(1) default 0 not null,
    is_ready     tinyint(1) default 0 not null,
    is_canceled  tinyint(1) default 0 not null,
    created_at   timestamp            null,
    updated_at   timestamp            null,
    constraint order_details_order_id_foreign
        foreign key (order_id) references orders (id)
            on delete cascade,
    constraint order_details_product_id_foreign
        foreign key (product_id) references products (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;

create table payments
(
    id             bigint unsigned auto_increment
        primary key,
    order_id       bigint unsigned                                                 not null,
    table_id       bigint unsigned                                                 null,
    amount         decimal(10, 2)                                                  not null,
    payment_method enum ('nakit', 'kredi_kartı')              default 'nakit'      not null,
    status         enum ('bekleniyor', 'tamamlandı', 'iptal') default 'bekleniyor' not null,
    paid_at        timestamp                                                       null,
    created_at     timestamp                                                       null,
    updated_at     timestamp                                                       null,
    constraint payments_order_id_foreign
        foreign key (order_id) references orders (id)
            on delete cascade,
    constraint payments_table_id_foreign
        foreign key (table_id) references tables (id)
            on delete set null
)
    collate = utf8mb4_unicode_ci;

create table stock_movements
(
    id             bigint unsigned auto_increment
        primary key,
    stock_id       bigint unsigned         not null,
    order_id       bigint unsigned         null,
    quantity       int                     not null,
    type           enum ('giris', 'cikis') not null,
    description    text                    null,
    purchase_price decimal(10, 2)          null,
    sale_price     decimal(10, 2)          null,
    arrival_date   timestamp               null,
    created_at     timestamp               null,
    updated_at     timestamp               null,
    constraint stock_movements_order_id_foreign
        foreign key (order_id) references orders (id)
            on delete cascade,
    constraint stock_movements_stock_id_foreign
        foreign key (stock_id) references stocks (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;


