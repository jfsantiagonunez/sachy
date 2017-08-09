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
        theAccountId=row[0].to_i
        if (theAccountId!=cachedName)
          puts("#########Account name #{theAccountId}")
          accountId=findAccount(accounts,theAccountId)
          if accountId.nil?
            next
          else
            cachedId=accountId
            cachedName=theAccountId
          end
        end
        puts(accounts)
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
  
  
  # Grouping data
  TAG_MONTH_GROUP="Group by Months"
  TAG_DATASET_GROUP="Group by Datasets"
  TAG_MONTH_GROUP_TYPE=0
  TAG_DATASET_GROUP_TYPE=1
  
  # Value Representation
  TAG_VALUE_ABSOLUTE = "Show Absolute Value"
  TAG_VALUE_PERCENTAGE = "Show Percentage Value"
  TAG_VALUE_NEGATIVE = "Show as Negative Value"
  
  TAG_VALUE_ABSOLUTE_TYPE=0
  TAG_VALUE_PERCENTAGE_TYPE=1
  TAG_VALUE_NEGATIVE_TYPE=2
  
  def getCategoryData(account)
    return getMonthlyValues(account,true,12,TAG_MONTH_GROUP_TYPE,TAG_VALUE_ABSOLUTE_TYPE,false)
  end
  
  def getGroupCategoryData(account)
    return getMonthlyValues(account,true,12,TAG_MONTH_GROUP_TYPE,TAG_VALUE_ABSOLUTE_TYPE,true)
  end
  
  def getMonthlyValues(account,sum_calculation,period,group_type,value_type,group_category)

    featureDatasets={}
    dataSet = getSampleValuesForAccount(account,period,group_category)
    dataSet.each do |key,values|
      results = nil
      # this produces something like [{"april"=>33,"may"=>22}
      if sum_calculation
        results = getSumMonthlyValues(values) 
      else
        results = getAverageMonthlyValues(values) 
      end
      featureDatasets[key]=results
    end

    case group_type 
    when TAG_MONTH_GROUP_TYPE
       return orderPerMonths(featureDatasets,value_type) 
    when TAG_DATASET_GROUP_TYPE
       return orderPerFeatures(featureDatasets,value_type)
    end
  end
  
  def orderPerMonths(datasets,value_type)  
    # To display per month and the features stacked  
    totals = {}
    if value_type == TAG_VALUE_PERCENTAGE_TYPE
      # First calculate totals
      datasets.each do |key,dataset|
        dataset.each do |result|
          result.each do |month,value|
            if !totals.include?(month)
              totals[month] = 0
            end
            totals[month]+=value
          end 
        end
      end
    end  
      
    dataFeatures ={}
    datasets.each do |key,dataset|
      dataFeatures[key]={}
      dataset.each do |result|
        result.each do |month,value|
          dataFeatures[key][month]=getValueRepresentation(value,totals[month],value_type)
        end
      end
    end  
    return formatData(dataFeatures)
  end

  def getValueRepresentation(value,total,value_type)
    newValue = value
    if !value_type.nil?
      case value_type       
      when TAG_VALUE_PERCENTAGE_TYPE
        newValue = value * 100 / total
      when TAG_VALUE_NEGATIVE_TYPE
        newValue = -1 * value
      end
    end
    return newValue
  end

  def getSampleValuesForAccount(account,period,group_category)
    data={}
     
    categories =Transaction.where(account_id:account.id).group(:category_id).pluck(:category_id)
    groupCategories={}
    categories.each do |category|
      
      results = getValuesFromDataset(category,period,
                                     Category.find(category).ctype) # this produces something like [{"april"=>33,"may"=>22}
      
      if group_category 
        theGroupId = Category.find(category).grupo_id
        
        if groupCategories.include?(theGroupId) 
          theGroupName = groupCategories[theGroupId]
        else
          theGroupName=Grupo.find(theGroupId).name
          groupCategories[theGroupId] = theGroupName
          data[theGroupName]={}
        end
               
        results.each do |key,value|         
          if !data.include?(key)
            data[theGroupName][key]=0
          end
          data[theGroupName][key] += value
        end
      else 
        data[getLabelFromId(category)]=results
      end
    end
    
    return data
  end
  
  def getSumMonthlyValues(data)
    result = Hash[ data.map { |h, v| [Date.parse(h), v] } ].group_by{ |h, v| Date::MONTHNAMES[h.month] }.map { |m,v| { m => v.sum { |vv| vv.last } } }
  end
  
  def formatData(inputData)
    data = []
    inputData.each do |key,values|
      thisDataset = { "name" => key, "data" => values }
      data.push(thisDataset)
    end
    #TODO : SHOW DATA puts(data)
    return data
  end
  
  def getValuesFromDataset(category_id,period,category_type)
    data = {}
    values = nil
    if period == 0 || period.nil?
      values = Transaction.where( category_id: category_id).order(:transaction_date).pluck(:transaction_date,:amount)
    else
      backInTime = Date.today - (period - 1).month
      queryFrom = Date.new(backInTime.year, backInTime.month,1)
      
      values = Transaction.where( 'category_id = ? AND transaction_date >= ?',category_id,queryFrom).order(:transaction_date).pluck(:transaction_date,:amount)
    end
    
    values.each do |value|
      theValue = value[1]
      data[value[0].to_s]= category_type==2? -1*theValue : theValue  #Applying category type
    end
    return data
  end
  
  def getLabelFromId(category_id)
    return Category.find(category_id).name
  end
  

  TAG_W_LINEAR_NON_STACK="Lines Non Stack"
  TAG_W_LINEAR_STACK="Lines Stack"
  TAG_W_VERTICAL_BAR='Vertical Columns'
  TAG_W_HORIZONTAL_BAR='Horizontal Bars'
  TAG_W_PIE_CHART='Pie Chart'
  TAG_W_STACKED_VERTICAL_BAR='Stacked Columns'
  TAG_W_STACKED_HORIZONTAL_BAR='Stacked Bars'  
  
  TAG_W_LINEAR_NON_STACK_TYPE=0  # RU 1
  TAG_W_LINEAR_STACK_TYPE=1      #
  TAG_W_VERTICAL_BAR_TYPE=2      
  TAG_W_HORIZONTAL_BAR_TYPE=3    # LU-0
  TAG_W_PIE_CHART_TYPE=4         # RD 2
  TAG_W_STACKED_VERTICAL_BAR_TYPE=5  #LD 3
  TAG_W_STACKED_HORIZONTAL_BAR_TYPE=6
  
  def getLast12Months
    months={}
    backInTime = Date.today - 11.months
    for i in 1..12  
      theDate = Date.new(backInTime.year, backInTime.month,1)
      months[theDate.to_s]=theDate.strftime('%b %Y')
      backInTime += 1.month
    end
    return months
  end
  
  def getCategoryDataPerMonth(account,month)
    nextMonth = month.to_date + 1.month
    values = Transaction.where('account_id = ? AND transaction_date >= ? AND transaction_date < ?',account.id,month,nextMonth).group(:category_id).sum(:amount)
    categories = Category.all
    data={"actual"=>{},"budget"=>{}}
    dataGroup={"Start Saldo"=>{},"Balance"=>{},"End Saldo"=>{},"actual"=>{},"budget"=>{}}
    groupCategories={}
    saldo = 0 
    categories.each do |category|
      actual = values[category.id].nil?? 0 : values[category.id]
      saldo += actual
      toDisplay=actual
      if category.ctype == 0 || category.ctype == 2
        toDisplay=actual * -1
      end
      data["actual"][category.name] = toDisplay
      data["budget"][category.name] = category.budget 
   
      theGroupId = Category.find(category).grupo_id
      
      if groupCategories.include?(theGroupId) 
        theGroupName = groupCategories[theGroupId]
        dataGroup["actual"][theGroupName]+=toDisplay
      else
        theGroup=Grupo.find(theGroupId)
        theGroupName=theGroup.name
        groupCategories[theGroupId] = theGroupName
        dataGroup["actual"][theGroupName]=toDisplay
        dataGroup["budget"][theGroupName]=theGroup.budget        
      end
               
    end
    
    dataGroup["Balance"]["Summary"]=saldo    
    dataGroup["End Saldo"]["Summary"]=getLastSaldoForMonth(account,month,nextMonth)
    dataGroup["Start Saldo"]["Summary"]=getStartSaldoForMonth(account,month,nextMonth)
    
    return formatData(data),formatData(dataGroup)
  end
  
  def getStartSaldoForMonth(account,startMonth,endMonth)
    return Transaction.where('account_id = ? AND transaction_date >= ? AND transaction_date < ?', account.id,startMonth,endMonth).order('transaction_date asc').limit(1).pluck(:start_saldo)
  end
  
  def getLastSaldoForMonth(account,startMonth,endMonth)
    return Transaction.where('account_id = ? AND transaction_date >= ? AND transaction_date < ?', account.id,startMonth,endMonth).order('transaction_date desc').limit(1).pluck(:end_saldo)     
  end
  
  def getTransactionsForMonth(account,month)
    nextMonth = month.to_date + 1.month
    return Transaction.where('account_id = ? AND transaction_date >= ? AND transaction_date < ?',account.id,month,nextMonth).order(:transaction_date)
  end
  
  def getSaldoEvolution12Months(account)
    data={}
    theToday = Date.today
    backInTime = Date.new(theToday.year,theToday.month,1)
    
    for i in 1..12   
      saldo = getStartSaldoForMonth(account,backInTime,backInTime + 1.month) 
      if saldo.empty?
        # Try the last saldo of the previous month
        saldo = getLastSaldoForMonth(account,backInTime - 1.month ,backInTime)
      end
      data[backInTime.to_s]=saldo 
      backInTime -= 1.month
    end
    return data
  end
  
  def getCategoryHistoryPerAccount(account)
    months={}
    backInTime = Date.today - 11.months
    @lastMonth=Date.new(backInTime.year, backInTime.month,1)
    @oldMonth=nil
    for i in 1..12  
      theDate = Date.new(backInTime.year, backInTime.month,1)
      months[theDate.strftime('%b %Y')]=theDate
      backInTime += 1.month    
    end
    data={}
    vectorCategory={}
    
    Category.all.order(:ctype,:grupo_id).each do |category|
      name=category.name
      data[category.id]={}
      thisCatBudget=category.budget.nil?? '200' : category.budget
      thisAccCat = CategoryAccount.find_by(category_id:category.id, account_id:account.id)
      if !thisAccCat.nil? && !thisAccCat.budget.nil?
        thisAccCat = thisAccCat.budget
      end
      vectorCategory[category.id]={'name'=>name,
                                   'total'=>0,
                                   'ctype'=>category.ctype,
                                   'grupo_id'=>category.grupo_id,
                                   'budget'=>thisAccCat}
    end

    months.each do |nameMonth,thisMonthDate|
      datasMonth = Transaction.where('account_id = ? AND transaction_date >= ? AND transaction_date < ?',account.id,thisMonthDate,thisMonthDate + 1.month).group(:category_id).sum(:amount)
      datasMonth.each do |keyCat,value|
        vectorCategory[keyCat]['total']+=value
        data[keyCat][nameMonth]=value
      end
      if !datasMonth.empty?
        if @lastMonth < thisMonthDate
          @lastMonth = thisMonthDate
        end
        if @oldMonth.nil?
          @oldMonth = thisMonthDate
        elsif thisMonthDate < @oldMonth
          @oldMonth = thisMonthDate
        end
      end
    end 
    dataOut={}
    balance={}
    data.each do |keyCat,dataMonth|
      categoryName=vectorCategory[keyCat]['name']
      dataOut[categoryName]=data[keyCat]
      balance[keyCat]={}
      if !@oldMonth.nil? 
        @numberMonths = ((@lastMonth - @oldMonth).to_i / 30.0).round + 1
        theAverage = vectorCategory[keyCat]['total']/@numberMonths
        dataOut[categoryName]["Avg #{@numberMonths} m"]=theAverage
        dataOut[categoryName]['Total']=vectorCategory[keyCat]['total']
        vectorCategory[keyCat]['average']=theAverage
      end
    end
    return dataOut,vectorCategory
  end
  
  def getBalance(account)
    
  end
  
  def setBudget(params,account_id)
    category_id=params['category_id']
    thisACBudget = CategoryAccount.where(category_id:category_id, account_id:account_id).first_or_create()
    thisACBudget.budget=params['budget']
    thisACBudget.save
  end

end