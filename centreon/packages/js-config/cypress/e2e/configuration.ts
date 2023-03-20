/* eslint-disable global-require */
/* eslint-disable @typescript-eslint/no-var-requires */

import { defineConfig } from 'cypress';

import setupNodeEvents from './plugins';

interface CypressConfigOptions {
  cypressFolder?: string;
  dockerName?: string;
  isDevelopment?: boolean;
  specPattern: string;
}

export default ({
  specPattern,
  cypressFolder = 'cypress',
  isDevelopment = false,
  dockerName = 'centreon-dev'
}: CypressConfigOptions): Cypress.ConfigOptions => {
  const resultsFolder = `${cypressFolder}/results${
    isDevelopment ? '/dev' : ''
  }`;

  const baseUrlIPAddress = isDevelopment ? '0.0.0.0' : 'localhost';

  return defineConfig({
    chromeWebSecurity: false,
    defaultCommandTimeout: 6000,
    e2e: {
      baseUrl: `http://${baseUrlIPAddress}:4000`,
      excludeSpecPattern: ['*.js', '*.ts', '*.md'],
      experimentalSessionAndOrigin: true,
      setupNodeEvents,
      specPattern
    },
    env: {
      dockerName
    },
    execTimeout: 60000,
    reporter: 'mochawesome',
    reporterOptions: {
      html: false,
      json: true,
      overwrite: true,
      reportDir: `${resultsFolder}/reports`,
      reportFilename: '[name]-report.json'
    },
    requestTimeout: 10000,
    retries: 0,
    screenshotsFolder: `${resultsFolder}/screenshots`,
    video: true,
    videosFolder: `${resultsFolder}/videos`
  });
};
