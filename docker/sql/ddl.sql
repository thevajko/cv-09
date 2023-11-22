create table logins
(
    login       varchar(100)                         not null primary key,
    last_action datetime default current_timestamp() not null
);

create table messages
(
    id        int auto_increment primary key,
    recipient text default null,
    author    text not null,
    created   datetime default null,
    message   text not null
);

