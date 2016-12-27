module AccountsHelper
  require 'spreadsheet'
  
  include CategoriesHelper
  
  def findAccount(accounts,name)
    if accounts.include?(name)
      return accounts[name][:id]
    else
      account=Account.find_by(account_number: name)
      if account.nil?
        return nil
      else
        accounts[name]={:id=>account.id,:number=>0,:lastdate=>nil}
        return account.id
      end
    end
  end
  
  # Fields : accountNumber(0)  mutationcode(1)  transactiondate(2) valuedate(3) startsaldo(4)  endsaldo(5)  amount(6)  description(7)
  # Model:
    # "account_id",      
    # "transaction_date"
    # "start_saldo",                  
    # "end_saldo",                   
    # "amount",                       
    # "description",      
    # "created_at",                                  
    # "updated_at",                                  
    # "category_id",      
    
  def importTransactionsFile(filename)
    categories = Category.all
    accounts={}
    numberTrans=0
    book = Spreadsheet.open filename
    book.worksheets.each do |worksheet|
      skipfirst = true
      cachedId=nil
      cachedName=nil
      worksheet.each do |row|
        if skipfirst 
          skipfirst = false
          next
        end
        accountId=cachedId
        if (row[0]!=cachedName)
          accountId=findAccount(accounts,row[0])
          if accountId.nil?
            next
          else
            cachedId=accountId
            cachedName=row[0]
          end
        end
        
        Transaction.where( account_id: accountId, transaction_date: row[2], description: row[7] ).first_or_create(  ) do |transaction|
          transaction.start_saldo = row[4]
          transaction.end_saldo = row[5]
          transaction.amount = row[6]
          transaction.category_id = setCategory(categories,row[7])
        end
        accounts[cachedName][:number] +=1
        accounts[cachedName][:lastdate]=row[2]
      end
      storeAccountLastImport( accounts )
      File.delete(filename)
    end
  end
  
  def storeAccountLastImport( accounts )
    accounts.each do |key,account|
      theAccount = Account.find(account[:id])
      theAccount.last_import = DateTime.now
      theAccount.last_transaction = account[:lastdate]
      theAccount.last_amount_import = account[:number]
      theAccount.save
    end
  end
  
  def allAccounts
    accounts = Account.all
    if accounts.empty?
      createDefaults
      accounts = Account.all
    end
    return accounts
  end
  
  def createDefaults
    Account.where( name: 'Gatita', account_number: '404937587' ).first_or_create()
    Account.where( name: 'Lobito', account_number: '515840629' ).first_or_create()
    grupo = Grupo.where( name: 'Extra', budget: '300.00').first_or_create()
    Category.where( name: 'Other', budget: '100.00', grupo_id: grupo.id).first_or_create()
  end
  
  def setCategory(rules,description)
    rules.each do |rule|
      if rules.name.nil?
        next
      end
      result=description.scan(/#{rule.name}/)
      if !result.empty?
        return rule.category_id
      end
    end
    return 1
  end
  
  def queryTransactionsPerCategory(category_id,account_id)
    if account_id.nil?
      return Transaction.where(category_id: category_id)
    else
      return Transaction.where(category_id: category_id,account_id: account_id)
    end
  end
  
  def getGruposCategories
    return Grupo.joins(:categories).all
  end
  
  def resetCategory
    transactions = Transaction.all
    rules = Rule.all
    transactions.each do |transaction|
      transaction.category_id = setCategory(rules,transaction.description)
      transaction.save
    end
  end
end


