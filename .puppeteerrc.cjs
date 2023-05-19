const {join} = require('path');

/**
 * @type {import("puppeteer").Configuration}
 */
module.exports = {
  // Changes the cache location for Puppeteer.
  // cacheDirectory: join(__dirname, '.cache', 'puppeteer'),
  // cacheDirectory: 'C:\Users\DanTang\.cache\puppeteer',
  cacheDirectory: join('C:', 'Users', 'DanTang', '.cache', 'puppeteer'),
};