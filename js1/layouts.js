var frames,
    configIndex = 0,
    co = new Confirm(),
    MainTab,
    pages = {
        inside: {
            containers: [
                ['#Users', 0, 100],
                ['#MessageForm', 500],
                ['#Messages', 500, 100],
                '#MessagesContainer',
                ['#Status', 660],
                '#AlertContainer'
            ],
            onResize: function() {
                this.frames[5].Replace(-1, -1, this.winSize.width, this.winSize.height);
                layoutConfigs[configIndex].call(this);
                $('body').removeClass().addClass('Layout' + configIndex);
            },
            onLoad: function() {
                this.users = new Collection();
                this.rooms = new Collection();
                this.recepients = new Collection();
                this.co = new Confirm();
                this.CurrentRoomId = 1;
                this.me = null;
                this.tabs = new Tabs($("#Messages")[0], $("#MessagesContainer")[0]);
                this.MainTab = new Tab(1, "Чат", 1);
                this.CurrentTab;

                this.tabs.Add(this.MainTab);
                this.CurrentTab = this.MainTab;
                this.tabs.Print();

                HistoryGo(0);

                $('#AlertContainer').hide();
                this.co.Init("AlertContainer", "AlertBlock");

                if (this.Pong) this.Ping();
            }
        },
        info: {
            containers: ['#InfoContainer', '#InfoContent'],
            onResize: function() {
                this.frames[0].Replace(10, 10, this.winSize.width - 20, this.winSize.height - 20);
                this.frames[1].Replace(-1, -1, -1, this.frames[0].height - 40);
            },
            onLoad: function() {
                this.tabs = new Tabs($('#InfoContainer')[0], $('#InfoContent')[0]);
                this.tabs.Add(new Tab(1, 'Инфо', 1), $('#Info')[0]);
                this.tabs.Print();
            }
        },
        menu: {
            containers: [
                ['#OptionsContainer', 580, 400],
                '#OptionsContent',
                '#AlertContainer'
            ],
            onResize: function() {
                this.frames[0].Replace(10, 10, this.winSize.width - 20, winSize.height - 20);
                this.frames[1].Replace(-1, -1, -1, this.frames[0].height - 40);
                this.frames[2].Replace(-1, -1, this.winSize.width, this.winSize.height);
            },
            onLoad: function() {
                if (!this.opener) return;
                this.me = this.opener.me;
                if (this.me) {
                    this.UploadFrame = $('#uploadFrame')[0];

                    this.tabs = new Tabs($('#OptionsContainer')[0], $('#OptionsContent')[0]);
                    this.ProfileTab = new Tab(1, 'Личные данные', 1, '');
                    this.tabs.Add(this.ProfileTab);

                    this.tabs.Add(new Tab(
                        2, 'Настройки', true, '',
                        function (tab) {
                            new Settings().loadTemplate(tab, me.Id);
                        }
                    ));

                    this.tabs.Add(new Tab(
                        3, 'Журнал', true, '',
                        function (tab) {
                            new JournalsManager().loadTemplate(tab, me.Id);
                        }
                    ));

                    this.tabs.Add(new Tab(
                        5, 'Сообщения', true, '',
                        function (tab) {
                            new Wakeups().loadTemplate(tab, me.Id);
                        }
                    ));

                    if (this.me.Rights >= this.adminRights) {
                        this.tabs.Add(new Tab(
                            6, 'Пользователи', true, '',
                            function (tab) {
                                new Userman().loadTemplate(tab,me.Id);
                            }
                        ));

                        this.MainTab = new Tab(
                            7, 'Администрирование', 1, '',
                            function (tab) {
                                new AdminOptions().loadTemplate(tab, me.Id);
                            });
                        this.tabs.Add(MainTab);
                    } else {
                        this.MainTab = this.ProfileTab;
                    }
                    this.tabs.Print();
                    new Profile().loadTemplate(ProfileTab, me.Id);
                }
            }
        },
        wakeup: {
            containers: [
                ['#WakeupContainer', 400],
                ['#WakeupReply', 400]
            ],
            onResize: function() {
                this.frames[0].Replace(10, 40, this.winSize.width - 20, this.winSize.height - 50 - offset);
                this.frames[1].Replace(10, this.winSize.height - replyFormHeight, this.winSize.width - 20, replyFormHeight - 10);
            }
        }
    };

function initLayout(layout) {
    var context = {
        winSize: new MyFrame(window),
        frames: layout.containers.map(function(params) {
            params = _.flatten([params, null, null]);
            return new MyFrame($(params[0])[0], params[1], params[2]);
        })
    },
        onResize = function() {
            context.winSize.GetPosAndSize();
            layout.onResize.call(context);
        };

    $(window).on('resize', onResize);
    onResize();
    if (layout.onLoad) $(window).on('load', layout.onLoad.bind(window));
};
