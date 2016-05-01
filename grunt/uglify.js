module.exports = {
	all: {
		files  : {
			'js/menu-item-types.min.js': ['js/menu-item-types.js']
		},
		options: {
			banner   : '/*! <%= package.title %> - v<%= package.version %>\n' +
			' * <%= package.homepage %>\n' +
			' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
			' * Licensed GPLv2+' +
			' */\n',
			sourceMap: true,
			mangle   : {
				except: ['jQuery']
			}
		}
	}
}
