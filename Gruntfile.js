/* jshint node:true */
module.exports = function( grunt ) {
    'use strict';

    grunt.initConfig({

        //Metadata
        pkg: grunt.file.readJSON( 'package.json' ),

        // Setting folder templates.
        dirs: {
            css: 'assets/css',
            sass: 'assets/sass',
            fonts: 'assets/fonts',
            images: 'assets/images',
            esnext: 'assets/esnext',
            js: 'assets/js'
        },

        // JavaScript linting with JSHint.
        jshint: {
            options: {
                jshintrc: '.jshintrc'
            },
            all: [
                'Gruntfile.js',
                '<%= dirs.js %>/**/*.js',
                '!<%= dirs.js %>/**/*.min.js'
            ]
        },

        // Sass linting with Stylelint.
        stylelint: {
            options: {
                configFile: '.stylelintrc'
            },
            all: [
                '<%= dirs.sass %>/*.scss'
            ]
        },

        // Build .js files from esnext .js files.
        browserify: {
            options: {
                browserifyOptions: { debug: true },
                transform: [["babelify", { "presets": ["@babel/preset-env"] }]],
                watch: false,
                keepAlive: false,
            },
            blocks: {
                files: [{
                    expand: true,
                    cwd: '<%= dirs.esnext %>/blocks/',
                    src: [
                        '*.js'
                    ],
                    dest: '<%= dirs.js %>/blocks/',
                    ext: '.js'
                }]
            }
        },

        // Minify .js files.
        uglify: {
            options: {
                ie8: true,
                parse: {
                    strict: false
                },
                output: {
                    comments : /@license|@preserve|^!/
                }
            },
            main: {
                files: [{
                    expand: true,
                    cwd: '<%= dirs.js %>/',
                    src: [
                        '*.js',
                        '!*.min.js'
                    ],
                    dest: '<%= dirs.js %>/',
                    ext: '.min.js'
                }]
            },
            admin: {
                files: [{
                    expand: true,
                    cwd: '<%= dirs.js %>/admin/',
                    src: [
                        '*.js',
                        '!*.min.js'
                    ],
                    dest: '<%= dirs.js %>/admin/',
                    ext: '.min.js'
                }]
            },
            blocks: {
                files: [{
                    expand: true,
                    cwd: '<%= dirs.js %>/blocks/',
                    src: [
                        '*.js',
                        '!*.min.js'
                    ],
                    dest: '<%= dirs.js %>/blocks/',
                    ext: '.min.js'
                }]
            },
            frontend: {
                files: [{
                    expand: true,
                    cwd: '<%= dirs.js %>/frontend/',
                    src: [
                        '*.js',
                        '!*.min.js'
                    ],
                    dest: '<%= dirs.js %>/frontend/',
                    ext: '.min.js'
                }]
            }
        },

        // Compile all .scss files.
        sass: {
            compile: {
                options: {
                    implementation: require('node-sass'),
                    sourceMap: 'none'
                },
                files: [{
                    expand: true,
                    cwd: '<%= dirs.sass %>/',
                    src: ['*.scss'],
                    dest: '<%= dirs.css %>/',
                    ext: '.css'
                }]
            }
        },

        // Generate RTL .css files
        rtlcss: {
            main: {
                expand: true,
                cwd: '<%= dirs.css %>',
                src: [
                    '*.css',
                    '!*-rtl.css'
                ],
                dest: '<%= dirs.css %>/',
                ext: '-rtl.css'
            }
        },

        // Minify all .css files.
        cssmin: {
            minify: {
                expand: true,
                cwd: '<%= dirs.css %>/',
                src: ['*.css'],
                dest: '<%= dirs.css %>/',
                ext: '.css'
            }
        },

        // Autoprefixer.
        postcss: {
            options: {
                processors: [
                    require( 'autoprefixer' )
                ]
            },
            dist: {
                src: [
                    '<%= dirs.css %>/*.css'
                ]
            }
        },

        // Watch changes for assets.
        watch: {
            css: {
                files: ['<%= dirs.sass %>/*.scss'],
                tasks: ['sass', 'rtlcss', 'cssmin']
            },
            js: {
                files: [
                    '<%= dirs.js %>/*js',
                    '!<%= dirs.js %>/*.min.js'
                ],
                tasks: ['jshint', 'uglify']
            }
        },

        // Generate POT files.
        makepot: {
            options: {
                type: 'wp-plugin',
                domainPath: 'languages',
                potHeaders: {
                    'report-msgid-bugs-to': 'https://github.com/madrasthemes/masvideos/issues',
                    'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
                }
            },
            dist: {
                options: {
                    potFilename: '<%= pkg.name %>.pot',
                    exclude: [
                        'apigen/.*',
                        'tests/.*',
                        'tmp/.*'
                    ]
                }
            }
        },

        // Check textdomain errors.
        checktextdomain: {
            options:{
                text_domain: '<%= pkg.name %>',
                keywords: [
                    '__:1,2d',
                    '_e:1,2d',
                    '_x:1,2c,3d',
                    'esc_html__:1,2d',
                    'esc_html_e:1,2d',
                    'esc_html_x:1,2c,3d',
                    'esc_attr__:1,2d',
                    'esc_attr_e:1,2d',
                    'esc_attr_x:1,2c,3d',
                    '_ex:1,2c,3d',
                    '_n:1,2,4d',
                    '_nx:1,2,4c,5d',
                    '_n_noop:1,2,3d',
                    '_nx_noop:1,2,3c,4d'
                ]
            },
            files: {
                src:  [
                    '**/*.php',         // Include all files
                    '!apigen/**',       // Exclude apigen/
                    '!node_modules/**', // Exclude node_modules/
                    '!tests/**',        // Exclude tests/
                    '!vendor/**',       // Exclude vendor/
                    '!tmp/**'           // Exclude tmp/
                ],
                expand: true
            }
        },

        // Clean the directory.
        clean: {
            main: [
                '<%= pkg.name %>/',
                '<%= pkg.name %>*.zip'
            ]
        },

        // Creates deploy-able plugin
        copy: {
            main: {
                files: [ {
                    expand: true,
                    src: [
                        '**',
                        '!.*',
                        '!.*/**',
                        '.htaccess',
                        '!Gruntfile.js',
                        '!README.md',
                        '!package.json',
                        '!package-lock.json',
                        '!node_modules/**',
                        '!<%= pkg.name %>/**',
                        '!<%= pkg.name %>.zip',
                        '!assets/esnext/**',
                        '!none',
                        '!.DS_Store',
                        '!npm-debug.log'
                    ],
                    dest: '<%= pkg.name %>/'
                } ]
            }
        },

        compress: {
            build: {
                options: {
                    archive: '<%= pkg.name %>.zip',
                    mode: 'zip'
                },
                files: [ {
                    expand: true,
                    src: [
                        '**',
                        '!.*',
                        '!.*/**',
                        '.htaccess',
                        '!Gruntfile.js',
                        '!README.md',
                        '!package.json',
                        '!package-lock.json',
                        '!node_modules/**',
                        '!<%= pkg.name %>/**',
                        '!<%= pkg.name %>.zip',
                        '!assets/esnext/**',
                        '!none',
                        '!.DS_Store',
                        '!npm-debug.log'
                    ]
                } ]
            }
        }
    });

    // Load NPM tasks to be used here
    grunt.loadNpmTasks( 'grunt-sass' );
    grunt.loadNpmTasks( 'grunt-rtlcss' );
    grunt.loadNpmTasks( 'grunt-postcss' );
    grunt.loadNpmTasks( 'grunt-stylelint' );
    grunt.loadNpmTasks( 'grunt-wp-i18n' );
    grunt.loadNpmTasks( 'grunt-checktextdomain' );
    grunt.loadNpmTasks( 'grunt-contrib-jshint' );
    grunt.loadNpmTasks( 'grunt-contrib-uglify' );
    grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
    grunt.loadNpmTasks( 'grunt-contrib-watch' );
    grunt.loadNpmTasks( 'grunt-contrib-clean' );
    grunt.loadNpmTasks( 'grunt-contrib-copy' );
    grunt.loadNpmTasks( 'grunt-contrib-compress' );
    grunt.loadNpmTasks( 'grunt-browserify' );

    // Register tasks
    grunt.registerTask( 'default', [
        'js',
        'css'
    ]);

    grunt.registerTask( 'js', [
        // 'jshint',
        'browserify',
        'uglify'
    ]);

    grunt.registerTask( 'css', [
        'sass',
        'rtlcss',
        'postcss',
        'cssmin'
    ]);

    grunt.registerTask( 'dev', [
        'default',
        'checktextdomain',
        'makepot'
    ]);

    grunt.registerTask( 'deploy', [
        'clean:main',
        'copy:main'
    ]);

    grunt.registerTask( 'build', [
        'compress:build'
    ]);
};
