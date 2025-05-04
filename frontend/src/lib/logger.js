// frontend/src/lib/logger.js
class Logger {
    constructor() {
      this.logLevel = process.env.NODE_ENV === 'production' ? 'error' : 'debug';
      this.levels = {
        debug: 0,
        info: 1,
        warn: 2,
        error: 3
      };
    }
  
    shouldLog(level) {
      return this.levels[level] >= this.levels[this.logLevel];
    }
    
    formatMessage(message, ...args) {
      if (args.length === 0) {
        return message;
      }
  
      const timestamp = new Date().toISOString();
      const formattedArgs = args.map(arg => {
        if (arg instanceof Error) {
          return `${arg.message}\n${arg.stack}`;
        } else if (typeof arg === 'object') {
          try {
            return JSON.stringify(arg);
          } catch (e) {
            return String(arg);
          }
        }
        return String(arg);
      });
  
      return `[${timestamp}] ${message} ${formattedArgs.join(' ')}`;
    }
  
    debug(message, ...args) {
      if (this.shouldLog('debug')) {
        console.debug(this.formatMessage(message, ...args));
      }
    }
  
    info(message, ...args) {
      if (this.shouldLog('info')) {
        console.info(this.formatMessage(message, ...args));
      }
    }
  
    warn(message, ...args) {
      if (this.shouldLog('warn')) {
        console.warn(this.formatMessage(message, ...args));
      }
    }
  
    error(message, ...args) {
      if (this.shouldLog('error')) {
        console.error(this.formatMessage(message, ...args));
      }
    }
  
    setLogLevel(level) {
      if (this.levels[level] !== undefined) {
        this.logLevel = level;
      }
    }
  }
  
  export const logger = new Logger();