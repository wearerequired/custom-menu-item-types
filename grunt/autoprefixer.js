module.exports = {
	dist: {
		options: {
			browsers: [
				'last 2 versions',
				'> 5%',
				'ie 9'
			]
		},
		expand : true,
		flatten: true,
		src    : 'css/menu-item-types.css',
		dest   : 'css/'
	}
}
