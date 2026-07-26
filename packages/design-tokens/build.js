import StyleDictionary from 'style-dictionary';

const sd = new StyleDictionary({
  source: ['tokens.json'],
  platforms: {
    css: {
      transformGroup: 'css',
      buildPath: 'dist/',
      files: [{
        destination: 'tokens.css',
        format: 'css/variables'
      }]
    },
    js: {
      transformGroup: 'js',
      buildPath: 'dist/',
      files: [{
        destination: 'tokens.js',
        format: 'javascript/module'
      }]
    },
    php: {
      transforms: ['name/kebab'],
      buildPath: 'dist/',
      files: [{
        destination: 'filament-theme.php',
        format: 'php/filament'
      }]
    }
  }
});

// Custom format for Filament theme PHP array
sd.registerFormat({
  name: 'php/filament',
  format: function({ dictionary }) {
    let output = '<?php\n\nreturn [\n';
    dictionary.allTokens.forEach(token => {
      // e.g. 'primary-500' => '#3b82f6'
      output += `    '${token.name}' => '${token.value}',\n`;
    });
    output += '];\n';
    return output;
  }
});

sd.buildAllPlatforms();
