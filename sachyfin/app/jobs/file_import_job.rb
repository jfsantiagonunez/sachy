class FileImportJob < ActiveJob::Base
  queue_as :default
  self.queue_adapter = :sucker_punch
  include SuckerPunch::Job
  workers 4
  SuckerPunch.shutdown_timeout = 1500
  
  def perform(file)
    # Call the trama parser and import the file    
    importTransactionFile(file)
    puts( "Importing #{file} ")
  end
end
